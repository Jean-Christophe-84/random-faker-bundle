<?php

namespace RandomFakerBundle\Helper;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Exception;
use Faker\Factory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

readonly class FixtureHelper
{
    public function __construct(private EntityManagerInterface $entityManager, private ParameterBagInterface $parameterBag)
    {
    }

    public function camelToSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<=[a-z0-9])([A-Z])/', '_$1', $input));
    }

    public function getFields(ReflectionClass $reflectionClass, string $fieldName): array
    {
        $yamlFile = $this->getYamlFile($reflectionClass);

        $data = Yaml::parseFile($yamlFile['newFilePath'], flags: Yaml::PARSE_DATETIME);

        $dataFields = $data['fields'] ?? [];
        $fields     = [];

        if ($this->hasOrmAnnotations($reflectionClass)) {
            $type = 'Annotation';
        } elseif ($this->hasOrmAttributes($reflectionClass)) {
            $type = 'Attribute';
        } else {
            $type = null;
        }

        if (array_key_exists($fieldName, $dataFields) && $type) {
            foreach ($dataFields[$fieldName] as $key => $value) {
                $fields[] = [
                    'name'       => $this->getNameFaker($key, $type),
                    'nameFaker'  => $key,
                    'parameters' => $value['parameters'] ?? []
                ];
            }
        }

        return $fields;
    }

    public function getNameFaker(string $name, string $type): false|string
    {
        $finder = new Finder();
        $finder->files()
            ->in(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $type)
            ->name('Faker' . ucfirst($name) . '.php');

        $name = '';

        foreach ($finder as $file) {
            $name = $file->getRealPath();
        }

        return $name;
    }

    public function getOrder(ReflectionClass $reflectionClass): mixed
    {
        $yamlFile = $this->getYamlFile($reflectionClass);

        $data = Yaml::parseFile($yamlFile['newFilePath'], flags: Yaml::PARSE_DATETIME);

        return $data['order'] ?? null;
    }

    public function getRandomFaker(string $columnType, ?int $length = null, ?int $precision = null)
    {
        $faker = Factory::create('fr_FR');

        switch ($columnType) {
            case 'integer':
                if ($length) {
                    return $faker->numberBetween(0, $length);
                } else {
                    return $faker->numberBetween(-2147483648);
                }
            case 'bigint':
                if ($length) {
                    return $faker->numberBetween(0, $length);
                } else {
                    return $faker->numberBetween(-9223372036854775808, 9223372036854775807);
                }
            case 'smallint':
                if ($length) {
                    return $faker->numberBetween(0, $length);
                } else {
                    return $faker->numberBetween(-32768, 32767);
                }
            case 'string':
                if ($length) {
                    return $faker->text($length);
                } else {
                    return $faker->word();
                }
            case 'text':
                if ($length) {
                    return $faker->text($length);
                } else {
                    return $faker->text();
                }
            case 'boolean':
                return $faker->numberBetween(0, 1);
            case 'decimal':
            case 'float':
                if ($precision) {
                    return $faker->randomFloat($precision);
                } else {
                    return $faker->randomFloat();
                }
            case 'date':
                return $faker->date();
            case 'datetime':
                return $faker->dateTime();
            case 'datetimetz':
                return $faker->dateTimeAD();
            case 'time':
                return $faker->time();
            case 'array':
                return $faker->randomElement();
            case 'json':
                return json_encode($faker->randomElement());
            case 'object':
                return json_decode(json_encode($faker->randomElement()));
            case 'binary':
                return implode('', $faker->randomElements(['0', '1'], 16));
            case 'blob':
                return implode('', $faker->randomElements(['0', '1'], 32));
            case 'guid':
                return $faker->uuid();
            default:
                return $faker->word();
        }
    }

    public function getRecords(ReflectionClass $reflectionClass): mixed
    {
        $yamlFile = $this->getYamlFile($reflectionClass);

        $data = Yaml::parseFile($yamlFile['newFilePath'], flags: Yaml::PARSE_DATETIME);

        return $data['records'] ?? 0;
    }

    public function getTotalNumberOfEntity(array $entities, string $entityName): ?int
    {
        $records = null;

        foreach ($entities as $entity) {
            if ($entity['name'] === $entityName) {
                $records = $entity['records'];
            }
        }

        return $records;
    }

    public function getTypeAssociation(int $association): string
    {
        return match ($association) {
            ClassMetadataInfo::ONE_TO_ONE   => 'OneToOne',
            ClassMetadataInfo::MANY_TO_ONE  => 'ManyToOne',
            ClassMetadataInfo::ONE_TO_MANY  => 'OneToMany',
            ClassMetadataInfo::MANY_TO_MANY => 'ManyToMany',
            default => '',
        };
    }

    public function getYamlFile(ReflectionClass $reflectionClass): array
    {
        $originalFilePath = $reflectionClass->getFileName();

        // Récupération du chemin sans extension
        $filePathWithoutExt = pathinfo($originalFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($originalFilePath, PATHINFO_FILENAME);

        // Nouvelle extension à tester, par exemple ".txt"
        $newExtension = '.yaml';

        // Nouveau chemin avec la nouvelle extension
        $newFilePath = $filePathWithoutExt . $newExtension;

        return ['fileExists' => file_exists($newFilePath), 'newFilePath' => $newFilePath];
    }

    function hasOrmAnnotations(ReflectionClass $reflectionClass): bool
    {
        $hasOrmTag = function (?string $doc): bool {
            if (!$doc) return false;
            // Recherche rapide de tokens classiques Doctrine
            // @ORM\Entity, @ORM\Column, @Entity, @Column, etc.
            return (bool) preg_match('/@(?:ORM\\\\)?(Entity|Table|Column|Id|GeneratedValue|OneToOne|OneToMany|ManyToOne|ManyToMany)\b/', $doc);
        };

        if ($hasOrmTag($reflectionClass->getDocComment())) {
            return true;
        }

        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE) as $prop) {
            if ($hasOrmTag($prop->getDocComment())) {
                return true;
            }
        }

        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE) as $method) {
            if ($hasOrmTag($method->getDocComment())) {
                return true;
            }
        }

        return false;
    }

    function hasOrmAttributes(ReflectionClass $reflectionClass): bool
    {
        $checkAttributes = function (array $attributes): bool {
            foreach ($attributes as $attribute) {
                if ($attribute instanceof ReflectionAttribute) {
                    $name = ltrim($attribute->getName(), '\\');
                    if (str_starts_with($name, 'Doctrine\\ORM\\Mapping\\')) {
                        return true;
                    }
                    // Cas d’alias importé: use Doctrine\ORM\Mapping as ORM;
                    if (preg_match('#(^|\\\\)ORM($|\\\\)#', $name)) {
                        return true;
                    }
                }
            }

            return false;
        };

        if ($checkAttributes($reflectionClass->getAttributes())) {
            return true;
        }

        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE) as $prop) {
            if ($checkAttributes($prop->getAttributes())) {
                return true;
            }
        }

        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE) as $method) {
            if ($checkAttributes($method->getAttributes())) {
                return true;
            }
        }

        return false;
    }

    public function isAssociationNullable(ReflectionClass $reflectionClass, string $associationName, array $joinColumns): ?bool
    {
        if ($fields = $this->getFields($reflectionClass, $associationName)) {
            foreach ($fields as $field) {
                if (isset($field['nameFaker']) && $field['nameFaker'] == 'nullable') {
                    return $field['parameters']['nullable'] ?? null;
                }
            }
        }

        foreach ($joinColumns as $joinColumn) {
            return $joinColumn['nullable'] ?? null;
        }

        return null;
    }

    public function isAssociationUnique(ReflectionClass $reflectionClass, string $associationName, array $joinColumns): ?bool
    {
        if ($fields = $this->getFields($reflectionClass, $associationName)) {
            foreach ($fields as $field) {
                if (isset($field['nameFaker']) && $field['nameFaker'] == 'unique') {
                    return $field['parameters']['unique'] ?? false;
                }
            }
        }

        foreach ($joinColumns as $joinColumn) {
            return $joinColumn['unique'] ?? false;
        }

        return false;
    }

    /**
     * @throws ReflectionException
     */
    public function isGeneratedValue(string $className, string $propertyName): bool
    {
        $reflectionClass = new ReflectionClass($className);

        if ($this->hasOrmAnnotations($reflectionClass)) {
            $reader             = new AnnotationReader();
            $reflectionProperty = new ReflectionProperty($className, $propertyName);

            $generatedValue = $reader->getPropertyAnnotation($reflectionProperty, ORM\GeneratedValue::class);

            return $generatedValue !== null;
        } elseif ($this->hasOrmAttributes($reflectionClass)) {
            $reflectionProperty = new ReflectionProperty($className, $propertyName);

            $attributes = $reflectionProperty->getAttributes(ORM\GeneratedValue::class);

            return !empty($attributes);
        } else {
            return false;
        }
    }

    public function loadFixturesFromCSV(OutputInterface $output, string $table, array $datas): void
    {
        try {
            $path = $this->parameterBag->get('kernel.project_dir') . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
            $file = $path . $table . '.csv';

            // Spreadsheet creation
            $spreadsheet = new Spreadsheet();

            // Sheet creation
            $worksheet = $spreadsheet->setActiveSheetIndex(0);
            $worksheet->setTitle(substr($table, 0, 25));

            $columns = range('A', 'Z');
            for ($i = 1; $i <= 26; $i++) {
                $columnsInProgress = range('A', 'Z');
                foreach ($columnsInProgress as $columnInProgress) {
                    $columns[] = $columns[$i - 1] . $columnInProgress;
                }
            }

            foreach ($datas as $key => $data) {
                $counter = count($data);

                for ($i = 0; $i < $counter; $i++) {
                    $spreadsheet->getActiveSheet()->setCellValue($columns[$i] . ($key + 1), ($data[$i] ?? "NULL"));
                }
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet); // Excel creation
            $writer->save($file); // File saving

            $inputFileType = IOFactory::identify($path . $table . ".csv");
            /** @var Csv $reader */
            $reader = IOFactory::createReader($inputFileType);
            $reader->setDelimiter(",");

            $spreadsheet = $reader->load($path . $table . ".csv");
            $workSheet   = $spreadsheet->getActiveSheet();

            $nbRows = $workSheet->getHighestRow();
            if (strlen($workSheet->getHighestColumn()) >= 2) {
                $columns   = range('A', 'Z');
                $nbLetters = array_search(substr($workSheet->getHighestColumn(), 0, 1), $columns) + 1;
                for ($i = 1; $i <= $nbLetters; $i++) {
                    $columnsInProgress = $i != $nbLetters ? range('A', 'Z') : range('A', substr($workSheet->getHighestColumn(), -1));
                    foreach ($columnsInProgress as $columnInProgress) {
                        $columns[] = $columns[$i - 1] . $columnInProgress;
                    }
                }
            } else {
                $columns = range('A', $workSheet->getHighestColumn());
            }

            $progressBar = new ProgressBar($output, $nbRows - 1);
            $progressBar->setFormat('debug');

            $sql = "INSERT INTO `$table` (";

            $c = 1;
            foreach ($columns as $column) {
                $sql .= ($c == count($columns)) ? " `" . $workSheet->getCell($column . '1')->getValue() . "`)" :
                    "`" . $workSheet->getCell($column . '1')->getValue() . "`,";
                $c++;
            }

            $sql .= " VALUES ";
            for ($row = 2; $row <= $nbRows; $row++) {
                $sql .= "(";
                $c   = 1;
                foreach ($columns as $column) {
                    if (is_int($workSheet->getCell($column . $row)->getValue()) || $workSheet->getCell($column . $row)->getValue() == "NULL") {
                        $sql .= " " . $workSheet->getCell($column . $row)->getValue();
                    } else {
                        $sql .= " \"" . addslashes((string)$workSheet->getCell($column . $row)->getValue()) . "\"";
                    }
                    $sql .= ($c == count($columns)) ? ")" : ",";
                    $c++;
                }
                $sql .= ($row == $nbRows) ? ";" : ",";
                $progressBar->advance();
            }

            if ($nbRows >= 2) {
                $this->entityManager->getConnection()->executeStatement($sql);
            }  // Execute native SQL

            if (file_exists($file)) {
                shell_exec('rm -rf ' . $file);
            }
        } catch (Exception $exception) {
            exit($exception->getMessage());
        }
    }
}

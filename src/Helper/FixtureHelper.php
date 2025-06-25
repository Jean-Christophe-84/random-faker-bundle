<?php

namespace RandomFakerBundle\Helper;

use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Exception;
use Faker\Factory;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class FixtureHelper
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param ContainerInterface     $container
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container     = $container;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    public function camelToSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<=[a-z0-9])([A-Z])/', '_$1', $input));
    }

    /**
     * @param string $className
     * @param string $annotationClass
     * @param string $key
     *
     * @return null
     * @throws ReflectionException
     */
    public function getParameter(string $className, string $annotationClass, string $key)
    {
        $reflectionClass = new ReflectionClass($className);
        $reader          = new AnnotationReader();

        $annotations = $reader->getClassAnnotations($reflectionClass);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof $annotationClass) {
                return property_exists($annotation, $key) ? $annotation->$key : null;
            }
        }

        return null;
    }

    /**
     * @throws ReflectionException
     */
    public function getAnnotationsForEntity(string $entityClass, string $fieldName): array
    {
        $reflectionClass = new ReflectionClass($entityClass);
        $reader          = new AnnotationReader();
        $annotations     = [];
        $allClasses      = $this->getClassesInDirectory(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Annotation');

        foreach ($reflectionClass->getProperties() as $property) {
            if ($property->getName() === $fieldName) {
                foreach ($reader->getPropertyAnnotations($property) as $annotation) {
                    $reflection = new ReflectionClass($annotation);

                    if (in_array(get_class($annotation), $allClasses, true)) {
                        $annotations[] = [
                            'name'       => get_class($annotation),
                            'nameFaker'  => $this->getNameForFaker($reflection->getShortName()),
                            'parameters' => get_object_vars($annotation)
                        ];
                    }
                }
            }
        }

        return $annotations;
    }

    /**
     * @throws ReflectionException
     */
    public function getClassesInDirectory(string $directory): array
    {
        $finder = new Finder();
        $finder->files()->in($directory)->name('*.php');

        $classes = [];
        foreach ($finder as $file) {
            $filePath = $file->getRealPath();
            require_once $filePath;
            foreach (get_declared_classes() as $class) {
                $reflection = new ReflectionClass($class);

                if ($reflection->getFileName() === $filePath) {
                    $classes[] = $class;
                }
            }
        }

        return $classes;
    }

    /**
     * @param $fakerAttributeName
     *
     * @return string
     */
    public function getNameForFaker($fakerAttributeName): string
    {
        return lcfirst(substr($fakerAttributeName, 5));
    }

    /**
     * @param string   $columnType
     * @param int|null $length
     * @param int|null $precision
     *
     * @return DateTime|false|float|int|mixed|string
     */
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

    /**
     * @param array  $entities
     * @param string $entityName
     *
     * @return int|null
     */
    public function getTotalNumberOfEntity(array $entities, string $entityName): ?int
    {
        $number = null;

        foreach ($entities as $entity) {
            if ($entity['name'] === $entityName) {
                $number = $entity['number'];
            }
        }

        return $number;
    }

    /**
     * @param int $association
     *
     * @return string
     */
    public function getTypeAssociation(int $association): string
    {
        switch ($association) {
            case ClassMetadata::ONE_TO_ONE:
                return 'OneToOne';
            case ClassMetadata::MANY_TO_ONE:
                return 'ManyToOne';
            case ClassMetadata::ONE_TO_MANY:
                return 'OneToMany';
            case ClassMetadata::MANY_TO_MANY:
                return 'ManyToMany';
            default:
                return '';
        }
    }

    /**
     * @throws ReflectionException
     * @throws MappingException
     */
    public function isAssociationNullable(string $entityClass, string $associationName): ?bool
    {
        $metadata = $this->entityManager->getClassMetadata($entityClass);

        if (!$metadata->hasAssociation($associationName)) {
            throw new InvalidArgumentException("L'association '$associationName' n'existe pas dans la classe '$entityClass'.");
        }

        $associationMapping = $metadata->getAssociationMapping($associationName);

        if ($annotations = $this->getAnnotationsForEntity($entityClass, $associationName)) {
            foreach ($annotations as $annotation) {
                if (isset($annotation['nameFaker']) && $annotation['nameFaker'] == 'nullable') {
                    return $annotation['parameters']['nullable'] ?? null;
                }
            }
        }

        if (isset($associationMapping['joinColumns'])) {
            foreach ($associationMapping['joinColumns'] as $joinColumn) {
                return $joinColumn['nullable'] ?? null;
            }
        }

        return null;
    }

    /**
     * @throws ReflectionException
     */
    public function isGeneratedValue(string $className, string $propertyName): bool
    {
        $reader             = new AnnotationReader();
        $reflectionProperty = new ReflectionProperty($className, $propertyName);

        $generatedValue = $reader->getPropertyAnnotation($reflectionProperty, ORM\GeneratedValue::class);

        return $generatedValue !== null;
    }

    /**
     * @param OutputInterface $output
     * @param string          $table
     * @param array           $datas
     *
     * @return void
     */
    public function loadFixturesFromCSV(OutputInterface $output, string $table, array $datas): void
    {
        try {
            $path = $this->container->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
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
                $this->entityManager->getConnection()->executeQuery($sql);
            }  // Execute native SQL

            if (file_exists($file)) {
                shell_exec('rm -rf ' . $file);
            }
        } catch (Exception $exception) {
            exit($exception->getMessage());
        }
    }
}

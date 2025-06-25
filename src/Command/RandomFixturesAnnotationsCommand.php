<?php

namespace RandomFakerBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Faker\Factory;
use RandomFakerBundle\Annotation\FakerNumber;
use RandomFakerBundle\Annotation\FakerOrder;
use RandomFakerBundle\Helper\FixtureHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RandomFixturesAnnotationsCommand extends Command
{
    protected static               $defaultName = 'annotations:fixtures:random';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FixtureHelper
     */
    private $fixtureHelper;

    public function __construct(EntityManagerInterface $entityManager, FixtureHelper $fixtureHelper)
    {
        parent::__construct(self::$defaultName);
        $this->entityManager = $entityManager;
        $this->fixtureHelper = $fixtureHelper;
    }

    /**
     * Load random fixtures with Faker for entity with annotations
     */
    protected function configure(): void
    {
        $this->setDescription("Load random fixtures with Faker for entity with annotations");
    }

    /**
     * Execute the command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Not log SQL
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger();

        $faker    = Factory::create('fr_FR');
        $entities = [];
        $metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();

        usort($metaData, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        foreach ($metaData as $meta) {
            $entities[$meta->getReflectionClass()->getShortName()] = [
                'associations' => [],
                'columns'      => [],
                'name'         => $meta->getName(),
                'number'       => $this->fixtureHelper->getParameter($meta->getName(), FakerNumber::class, 'number'),
                'order'        => $this->fixtureHelper->getParameter($meta->getName(), FakerOrder::class, 'order'),
                'table'        => $meta->getTableName()
            ];

            foreach ($meta->getAssociationMappings() as $fieldName => $mapping) {
                $entities[$meta->getReflectionClass()->getShortName()]['associations'][$fieldName] = [
                    'annotations'  => $this->fixtureHelper->getAnnotationsForEntity($meta->getName(), $fieldName),
                    'nullable'     => $this->fixtureHelper->isAssociationNullable($meta->getName(), $fieldName),
                    'type'         => $this->fixtureHelper->getTypeAssociation($mapping['type']),
                    'targetEntity' => $mapping['targetEntity']
                ];
            }

            foreach ($meta->getFieldNames() as $fieldName) {
                $entities[$meta->getReflectionClass()->getShortName()]['columns'][$fieldName] = [
                    'annotations'    => $this->fixtureHelper->getAnnotationsForEntity($meta->getName(), $fieldName),
                    'generatedValue' => $this->fixtureHelper->isGeneratedValue($meta->getName(), $fieldName),
                    'length'         => $meta->getFieldMapping($fieldName)['length'] ?? null,
                    'nullable'       => $meta->getFieldMapping($fieldName)['nullable'] ?? false,
                    'precision'      => $meta->getFieldMapping($fieldName)['precision'] ?? null,
                    'type'           => $meta->getFieldMapping($fieldName)['type']
                ];
            }
        }

        $entities = array_filter($entities, function ($item) {
            return $item['order'] !== null;
        });

        usort($entities, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        foreach ($entities as $entity) {
            $output->writeln(['Add random fixtures for table "' . $entity['table'] . '"']);

            $datas = [];

            foreach ($entity['associations'] as $keyAS => $association) {
                foreach ($association['annotations'] as $keyAN => $annotation) {
                    if (isset($annotation['nameFaker']) && $annotation['nameFaker'] == 'ignore' && isset($annotation['parameters']['ignore'])
                        && $annotation['parameters']['ignore'] === true
                    ) {
                        unset($entity['associations'][$keyAS]);
                    }
                    if (isset($annotation['nameFaker']) && $annotation['nameFaker'] == 'nullable' && isset($annotation['parameters']['nullable'])) {
                        $entity['associations'][$keyAS]['nullable'] = $annotation['parameters']['nullable'];
                        unset($entity['associations'][$keyAS]['annotations'][$keyAN]);
                    }
                }
            }

            foreach ($entity['columns'] as $keyC => $column) {
                foreach ($column['annotations'] as $keyA => $annotation) {
                    if (isset($annotation['nameFaker']) && $annotation['nameFaker'] == 'ignore' && isset($annotation['parameters']['ignore'])
                        && $annotation['parameters']['ignore'] === true
                    ) {
                        unset($entity['columns'][$keyC]);
                    }
                    if (isset($annotation['nameFaker']) && $annotation['nameFaker'] == 'nullable' && isset($annotation['parameters']['nullable'])) {
                        $entity['columns'][$keyC]['nullable'] = $annotation['parameters']['nullable'];
                        unset($entity['columns'][$keyC]['annotations'][$keyA]);
                    }
                }
            }

            foreach ($entity['associations'] as $keyA => $association) {
                if ($association['type'] == $this->fixtureHelper->getTypeAssociation(ClassMetadata::MANY_TO_ONE)) {
                    $datas[0][] = $this->fixtureHelper->camelToSnakeCase($keyA) . '_id';
                }
            }

            foreach ($entity['columns'] as $keyC => $column) {
                $datas[0][] = $this->fixtureHelper->camelToSnakeCase($keyC);
            }

            for ($i = 1; $i <= $entity['number']; $i++) {
                foreach ($entity['associations'] as $association) {
                    if ($association['type'] == $this->fixtureHelper->getTypeAssociation(ClassMetadata::MANY_TO_ONE)) {
                        if ($totalNumberEntity = $this->fixtureHelper->getTotalNumberOfEntity($entities, $association['targetEntity'])) {
                            $datas[$i][] = $association['nullable'] && mt_rand(0, 1) ? null : mt_rand(1, $totalNumberEntity);
                        } else {
                            $datas[$i][] = null;
                        }
                    }
                }

                foreach ($entity['columns'] as $column) {
                    if ($column['generatedValue']) {
                        $datas[$i][] = $i;
                    } elseif (!empty($column['annotations'])) {
                        $parameters          = [];
                        $datasWithParameters = '';

                        foreach ($column['annotations'] as $annotation) {
                            if (!empty($annotation['parameters'])) {
                                foreach ($annotation['parameters'] as $parameter) {
                                    if ($parameter instanceof DateTime) {
                                        $today     = new DateTime();
                                        $interval  = $today->diff($parameter);
                                        $parameter = $interval->format('%R%a') . ' days';
                                    }
                                    $parameters[] = $parameter;
                                }
                            }
                            if (!empty($annotation['name'])) {
                                if (str_contains($annotation['name'], 'Locales') && preg_match('/Locales\\\\([^\\\\]+)/', $annotation['name'], $matches)) {
                                    $faker = Factory::create($matches[1]);

                                    $datasWithParameters = $column['nullable'] && mt_rand(0, 1) ? null : $faker->{'' . $annotation['nameFaker']}(...$parameters);

                                    $faker = Factory::create('fr_FR');
                                } else {
                                    $datasWithParameters = $column['nullable'] && mt_rand(0, 1) ? null : $faker->{'' . $annotation['nameFaker']}(...$parameters);
                                }
                            }
                        }

                        $datas[$i][] = $datasWithParameters;
                    } else {
                        $datas[$i][] = $column['nullable'] && mt_rand(0, 1) ? null : $this->fixtureHelper->getRandomFaker($column['type'], $column['length'], $column['precision']);
                    }
                }
            }

            $this->fixtureHelper->loadFixturesFromCSV($output, $entity['table'], $datas);

            $output->writeln(['']);
            $output->writeln(['Add random fixtures for table "' . $entity['table'] . '" finished']);
        }

        return 0;
    }
}

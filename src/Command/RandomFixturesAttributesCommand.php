<?php

namespace RandomFakerBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Faker\Factory;
use Override;
use RandomFakerBundle\Attribute\FakerNumber;
use RandomFakerBundle\Attribute\FakerOrder;
use RandomFakerBundle\Helper\FixtureHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name       : 'RandomFixturesAttributesCommand',
    description: 'Load random fixtures with Faker for entity with attributes',
    aliases    : ['attributes:fixtures:random']
)]
class RandomFixturesAttributesCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly FixtureHelper $fixtureHelper)
    {
        parent::__construct(AsCommand::class);
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Not log SQL
        $this->entityManager->getConnection()->getConfiguration()->setMiddlewares([]);

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
                'number'       => $this->fixtureHelper->getAttributeParameter($meta->getName(), FakerNumber::class, 'number'),
                'order'        => $this->fixtureHelper->getAttributeParameter($meta->getName(), FakerOrder::class, 'order'),
                'table'        => $meta->getTableName()
            ];

            foreach ($meta->getAssociationMappings() as $fieldName => $mapping) {
                $entities[$meta->getReflectionClass()->getShortName()]['associations'][$fieldName] = [
                    'attributes'   => $this->fixtureHelper->getAttributesForEntity($meta->getName(), $fieldName),
                    'nullable'     => $this->fixtureHelper->isAssociationNullableAttribute($meta->getName(), $fieldName),
                    'type'         => $this->fixtureHelper->getTypeAssociation($mapping['type']),
                    'targetEntity' => $mapping['targetEntity']
                ];
            }

            foreach ($meta->getFieldNames() as $fieldName) {
                $entities[$meta->getReflectionClass()->getShortName()]['columns'][$fieldName] = [
                    'attributes'     => $this->fixtureHelper->getAttributesForEntity($meta->getName(), $fieldName),
                    'generatedValue' => $this->fixtureHelper->isGeneratedValueAttribute($meta->getName(), $fieldName),
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
                foreach ($association['attributes'] as $keyAN => $attribute) {
                    if (isset($attribute['nameFaker']) && $attribute['nameFaker'] == 'ignore' && isset($attribute['parameters']['ignore'])
                        && $attribute['parameters']['ignore'] === true
                    ) {
                        unset($entity['associations'][$keyAS]);
                    }
                    if (isset($attribute['nameFaker']) && $attribute['nameFaker'] == 'nullable' && isset($attribute['parameters']['nullable'])) {
                        $entity['associations'][$keyAS]['nullable'] = $attribute['parameters']['nullable'];
                        unset($entity['associations'][$keyAS]['attributes'][$keyAN]);
                    }
                }
            }

            foreach ($entity['columns'] as $keyC => $column) {
                foreach ($column['attributes'] as $keyA => $attribute) {
                    if (isset($attribute['nameFaker']) && $attribute['nameFaker'] == 'ignore' && isset($attribute['parameters']['ignore'])
                        && $attribute['parameters']['ignore'] === true
                    ) {
                        unset($entity['columns'][$keyC]);
                    }
                    if (isset($attribute['nameFaker']) && $attribute['nameFaker'] == 'nullable' && isset($attribute['parameters']['nullable'])) {
                        $entity['columns'][$keyC]['nullable'] = $attribute['parameters']['nullable'];
                        unset($entity['columns'][$keyC]['attributes'][$keyA]);
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
                    } elseif (!empty($column['attributes'])) {
                        $parameters          = [];
                        $datasWithParameters = '';

                        foreach ($column['attributes'] as $attribute) {
                            if (!empty($attribute['parameters'])) {
                                foreach ($attribute['parameters'] as $parameter) {
                                    if ($parameter instanceof DateTime) {
                                        $today     = new DateTime();
                                        $interval  = $today->diff($parameter);
                                        $parameter = $interval->format('%R%a') . ' days';
                                    }
                                    $parameters[] = $parameter;
                                }
                            }
                            if (!empty($attribute['name'])) {
                                if (str_contains($attribute['name'], 'Locales') && preg_match('/Locales\\\\([^\\\\]+)/', $attribute['name'], $matches)) {
                                    $faker = Factory::create($matches[1]);

                                    $datasWithParameters = $column['nullable'] && mt_rand(0, 1) ? null : $faker->{'' . $attribute['nameFaker']}(...$parameters);

                                    $faker = Factory::create('fr_FR');
                                } else {
                                    $datasWithParameters = $column['nullable'] && mt_rand(0, 1) ? null : $faker->{'' . $attribute['nameFaker']}(...$parameters);
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

        return Command::SUCCESS;
    }
}

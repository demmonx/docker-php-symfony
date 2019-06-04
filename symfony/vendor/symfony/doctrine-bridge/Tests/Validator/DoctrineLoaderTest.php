<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Doctrine\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Test\DoctrineTestHelper;
use Symfony\Bridge\Doctrine\Tests\Fixtures\BaseUser;
use Symfony\Bridge\Doctrine\Tests\Fixtures\DoctrineLoaderEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bridge\Doctrine\Validator\DoctrineLoader;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Tests\Fixtures\Entity;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ValidatorBuilder;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class DoctrineLoaderTest extends TestCase
{
    public function testLoadClassMetadata()
    {
        if (!method_exists(ValidatorBuilder::class, 'addLoader')) {
            $this->markTestSkipped('Auto-mapping requires symfony/validation 4.2+');
        }

        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addLoader(new DoctrineLoader(DoctrineTestHelper::createTestEntityManager()))
            ->getValidator()
        ;

        $classMetadata = $validator->getMetadataFor(new DoctrineLoaderEntity());

        $classConstraints = $classMetadata->getConstraints();
        $this->assertCount(2, $classConstraints);
        $this->assertInstanceOf(UniqueEntity::class, $classConstraints[0]);
        $this->assertInstanceOf(UniqueEntity::class, $classConstraints[1]);
        $this->assertSame(['alreadyMappedUnique'], $classConstraints[0]->fields);
        $this->assertSame('unique', $classConstraints[1]->fields);

        $maxLengthMetadata = $classMetadata->getPropertyMetadata('maxLength');
        $this->assertCount(1, $maxLengthMetadata);
        $maxLengthConstraints = $maxLengthMetadata[0]->getConstraints();
        $this->assertCount(1, $maxLengthConstraints);
        $this->assertInstanceOf(Length::class, $maxLengthConstraints[0]);
        $this->assertSame(20, $maxLengthConstraints[0]->max);

        $mergedMaxLengthMetadata = $classMetadata->getPropertyMetadata('mergedMaxLength');
        $this->assertCount(1, $mergedMaxLengthMetadata);
        $mergedMaxLengthConstraints = $mergedMaxLengthMetadata[0]->getConstraints();
        $this->assertCount(1, $mergedMaxLengthConstraints);
        $this->assertInstanceOf(Length::class, $mergedMaxLengthConstraints[0]);
        $this->assertSame(20, $mergedMaxLengthConstraints[0]->max);
        $this->assertSame(5, $mergedMaxLengthConstraints[0]->min);

        $alreadyMappedMaxLengthMetadata = $classMetadata->getPropertyMetadata('alreadyMappedMaxLength');
        $this->assertCount(1, $alreadyMappedMaxLengthMetadata);
        $alreadyMappedMaxLengthConstraints = $alreadyMappedMaxLengthMetadata[0]->getConstraints();
        $this->assertCount(1, $alreadyMappedMaxLengthConstraints);
        $this->assertInstanceOf(Length::class, $alreadyMappedMaxLengthConstraints[0]);
        $this->assertSame(10, $alreadyMappedMaxLengthConstraints[0]->max);
        $this->assertSame(1, $alreadyMappedMaxLengthConstraints[0]->min);
    }

    public function testFieldMappingsConfiguration()
    {
        if (!method_exists(ValidatorBuilder::class, 'addLoader')) {
            $this->markTestSkipped('Auto-mapping requires symfony/validation 4.2+');
        }

        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addXmlMappings([__DIR__.'/../Resources/validator/BaseUser.xml'])
            ->addLoader(
                new DoctrineLoader(
                    DoctrineTestHelper::createTestEntityManager(
                        DoctrineTestHelper::createTestConfigurationWithXmlLoader()
                    ), '{}'
                )
            )
            ->getValidator();

        $classMetadata = $validator->getMetadataFor(new BaseUser(1, 'DemoUser'));

        $constraints = $classMetadata->getConstraints();
        $this->assertCount(0, $constraints);
    }

    /**
     * @dataProvider regexpProvider
     */
    public function testClassValidator(bool $expected, string $classValidatorRegexp = null)
    {
        $doctrineLoader = new DoctrineLoader(DoctrineTestHelper::createTestEntityManager(), $classValidatorRegexp);

        $classMetadata = new ClassMetadata(DoctrineLoaderEntity::class);
        $this->assertSame($expected, $doctrineLoader->loadClassMetadata($classMetadata));
    }

    public function regexpProvider()
    {
        return [
            [true, null],
            [true, '{^'.preg_quote(DoctrineLoaderEntity::class).'$|^'.preg_quote(Entity::class).'$}'],
            [false, '{^'.preg_quote(Entity::class).'$}'],
        ];
    }
}

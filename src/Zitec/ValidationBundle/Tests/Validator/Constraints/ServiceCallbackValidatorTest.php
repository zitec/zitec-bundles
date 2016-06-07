<?php

namespace Zitec\ValidationBundle\Tests\Validator\Constraints;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Zitec\ValidationBundle\Tests\Mocks\ValidatorServiceMock;
use Zitec\ValidationBundle\Validator\Constraints\ServiceCallback;
use Zitec\ValidationBundle\Validator\Constraints\ServiceCallbackValidator;

class ServiceCallbackValidatorTest extends AbstractConstraintValidatorTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $containerMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->containerMock = $this->getMockBuilder(ContainerInterface::class)->getMock();

        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function createValidator()
    {
        return new ServiceCallbackValidator($this->containerMock);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage Couldn't identify the service "validator_service" targeted by the ServiceCallback constraint!
     */
    public function testExceptionIsThrownWhenServiceIsMissing()
    {
        $constraint = new ServiceCallback(['serviceId' => 'validator_service']);

        $this->containerMock
            ->method('has')
            ->with('validator_service')
            ->willReturn(false);

        $this->validator->validate('', $constraint);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage The method "validateWrong" of service "validator_service" targeted by the ServiceCallback constraint isn't callable!
     */
    public function testExceptionIsThrownWhenServiceMethodIsNotCallable()
    {
        $constraint = new ServiceCallback(['serviceId' => 'validator_service', 'method' => 'validateWrong']);

        $this->containerMock
            ->method('has')
            ->with('validator_service')
            ->willReturn(true);
        $this->containerMock
            ->method('get')
            ->with('validator_service')
            ->willReturn(new ValidatorServiceMock());

        $this->validator->validate('', $constraint);
    }


    public function testViolationIsRaisedByDelegatedServiceMethod()
    {
        $constraint = new ServiceCallback(['serviceId' => 'validator_service', 'method' => 'validate']);

        $this->containerMock
            ->method('has')
            ->with('validator_service')
            ->willReturn(true);
        $this->containerMock
            ->method('get')
            ->with('validator_service')
            ->willReturn(new ValidatorServiceMock());

        $this->validator->validate('', $constraint);

        $this->buildViolation('')
            ->assertRaised();
    }

    public function testViolationIsRaisedByDelegatedService()
    {
        $constraint = new ServiceCallback(['serviceId' => 'validator_service']);

        $this->containerMock
            ->method('has')
            ->with('validator_service')
            ->willReturn(true);
        $this->containerMock
            ->method('get')
            ->with('validator_service')
            ->willReturn(new ValidatorServiceMock());

        $this->validator->validate('', $constraint);

        $this->buildViolation('')
            ->assertRaised();
    }

    public function testNoViolationIsRaisedByDelegatedServiceMethod()
    {
        $constraint = new ServiceCallback(['serviceId' => 'validator_service', 'method' => 'validate']);

        $this->containerMock
            ->method('has')
            ->with('validator_service')
            ->willReturn(true);
        $this->containerMock
            ->method('get')
            ->with('validator_service')
            ->willReturn(new ValidatorServiceMock(false));

        $this->validator->validate('', $constraint);

        $this->assertNoViolation();
    }
}

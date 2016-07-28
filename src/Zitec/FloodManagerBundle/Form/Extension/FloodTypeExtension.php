<?php
namespace Zitec\FloodManagerBundle\Form\Extension;

use Zitec\FloodManagerBundle\Form\EventListener\FloodValidationListener;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Zitec\FloodManagerBundle\Service\Manager;

class FloodTypeExtension extends AbstractTypeExtension
{
    /**
     * @var Manager
     */
    protected $floodManager;

    /**
     * @var bool|false
     */
    protected $defaultEnabled;

    /**
     * The default time interval, in minutes, for counting the form submits
     *
     * @var integer
     */
    protected $defaultTimeInterval;

    /**
     * The default number of attempts the user is allowed in the specified time interval
     *
     * @var integer
     */
    protected $defaultNumberOfAttempts;

    /**
     * @var TranslatorInterface|null
     */
    protected $translator;

    /**
     * @var null|string
     */
    protected $translationDomain;

    /**
     * @var string
     */
    protected $clientIp;

    /**
     * Default class constructor
     *
     * @param Manager $floodManager
     * @param TranslatorInterface|null $translator
     * @param RequestStack $requestStack ,
     * @param null $translationDomain
     * @param bool $defaultEnabled
     * @param int $defaultTimeInterval
     * @param int $defaultNumberOfAttempts
     */
    public function __construct(
        Manager $floodManager,
        TranslatorInterface $translator = null,
        RequestStack $requestStack,
        $translationDomain = null,
        $defaultEnabled = false,
        $defaultTimeInterval = 60,
        $defaultNumberOfAttempts = 10
    ) {
        $this->floodManager = $floodManager;
        $this->defaultEnabled = $defaultEnabled;
        $this->defaultTimeInterval = $defaultTimeInterval;
        $this->defaultNumberOfAttempts = $defaultNumberOfAttempts;
        $this->clientIp = $requestStack->getCurrentRequest()->getClientIp();

        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /*
         * Do not add the event listener if the flood protection is not enabled
         */
        if (empty($options['flood_enabled'])) {
            return;
        }

        $builder->addEventSubscriber(
            new FloodValidationListener(
                $this->floodManager,
                $this->translator,
                $this->translationDomain,
                $this->clientIp,
                $this->defaultTimeInterval,
                $this->defaultNumberOfAttempts,
                $options['flood_error_message']
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return Form::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'flood_enabled' => $this->defaultEnabled,
            'flood_error_message' => 'Your request cannot be processed at this time. Please try again later.'
        ));
    }

    /**
     * Sets the number of form submit attempts the user is allowed to send
     *
     * @param $attemptsNumber
     */
    public function setAttemptsNumber($attemptsNumber)
    {
        if (!empty($attemptsNumber) && is_numeric($attemptsNumber)) {
            $this->defaultNumberOfAttempts = (int) $attemptsNumber;
        }
    }

    /**
     * Sets the time interval used to track form submit attempts
     *
     * @param $timeInterval
     */
    public function setTimeInterval($timeInterval)
    {
        if (!empty($timeInterval) && is_numeric($timeInterval)) {
            $this->defaultTimeInterval = (int) $timeInterval;
        }
    }
}

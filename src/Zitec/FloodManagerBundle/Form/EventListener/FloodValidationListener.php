<?php

namespace Zitec\FloodManagerBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Zitec\FloodManagerBundle\Service\Manager;

class FloodValidationListener implements EventSubscriberInterface
{

    /**
     * @var Manager
     */
    protected $floodManager;

    /**
     * The default time interval, in minutes, for counting the form submits
     *
     * @var integer
     */
    protected $timeInterval;

    /**
     * The default number of attempts the user is allowed in the specified time interval
     *
     * @var integer
     */
    protected $numberOfAttempts;

    /**
     * The client's IP address used for validating
     *
     * @var string
     */
    protected $clientIp;

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
    protected $errorMessage;

    /**
     * Default class constructor
     *
     * @param Manager $floodManager
     * @param TranslatorInterface|null $translator
     * @param null $translationDomain
     * @param string $clientIp
     * @param int $timeInterval
     * @param int $numberOfAttempts
     * @param null $errorMessage
     */
    public function __construct(
        Manager $floodManager,
        TranslatorInterface $translator = null,
        $translationDomain = null,
        $clientIp,
        $timeInterval = 60,
        $numberOfAttempts = 10,
        $errorMessage
    ) {
        $this->floodManager = $floodManager;
        $this->timeInterval = $timeInterval;
        $this->numberOfAttempts = $numberOfAttempts;

        $this->clientIp = $clientIp;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->errorMessage = $errorMessage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * Check that the user is allowed to submit the form once more
     * Add the proper error and remove the field from the data
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        if ($form->isRoot() && $form->getConfig()->getOption('compound')) {
            if (!$this->floodManager->isEventAllowed(
                $this->getFormFloodEvent($form),
                $this->clientIp,
                $this->numberOfAttempts,
                $this->timeInterval * 60 // the time interval is in minutes, we will convert it to seconds
            )
            ) {
                $errorMessage = $this->errorMessage;
                if (null !== $this->translator) {
                    $errorMessage = $this->translator->trans($errorMessage, array(), $this->translationDomain);
                }
                $form->addError(new FormError($errorMessage));
            }

            //add flood entry for the current form and client
            $this->floodManager->addEntry($this->getFormFloodEvent($form), $this->clientIp, $this->timeInterval);

            $event->setData($data);
        }
    }

    /**
     * Return the flood event for the specified form
     *
     * @param FormInterface $form
     *
     * @return string
     */
    private function getFormFloodEvent(FormInterface $form)
    {
        return "form::{$form->getName()}";
    }
}

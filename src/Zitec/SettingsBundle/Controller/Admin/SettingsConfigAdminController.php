<?php

namespace Zitec\SettingsBundle\Controller\Admin;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CRUDController;

/**
 * The SEO Config entity admin controller.
 */
class SettingsConfigAdminController extends CRUDController
{
    /**
     * Reprocess the application settings
     *
     * @return Response
     *
     * @throws AccessDeniedException
     * @throws BadRequestHttpException
     */
    public function generateAction()
    {
        // Make security checks.
        if (false === $this->admin->isGranted('GENERATE')) {
            throw new AccessDeniedException();
        }

        try {
            // Generate the settings..
            $this->get('zitec.settings.settings_config_generator')->generate();
            // Display a success message.
            $this->addFlash('sonata_flash_success',
                $this->admin->trans('flash_generate_settings_config_success', array(), $this->admin->getTranslationDomain()));
        } catch (\Exception $exception) {
            // Display an error message if the operation failed.
            $this->addFlash('sonata_flash_error', $this->admin->trans('flash_generate_settings_config_error', array(), $this->admin->getTranslationDomain()));
        }

        return $this->redirect($this->admin->generateUrl('list'));
    }
}

<?php

namespace Drupal\fourspots_login\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Jwtlogout entity edit forms.
 *
 * @ingroup fourspots_login
 */
class JWTLogoutEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\fourspots_login\Entity\JWTLogoutEntity */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Jwtlogout entity.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Jwtlogout entity.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.jwt_logout_entity.canonical', ['jwt_logout_entity' => $entity->id()]);
  }

}

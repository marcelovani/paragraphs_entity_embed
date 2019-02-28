<?php

namespace Drupal\paragraphs_entity_embed\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\editor\EditorInterface;
use Drupal\embed\EmbedButtonInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Unicode;

/**
 * Controller that handle the CKEditor embed form for paragraphs.
 */
class ParagraphsEntityEmbedController extends ControllerBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   */
  protected $entityManager;

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $fs;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity.manager')->getStorage('embedded_paragraphs')
    );
  }

  /**
   * Constructs a EmbeddedParagraphs object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The custom embedded paragraphs storage.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityStorageInterface $storage) {
    $this->entityTypeManager = $entity_type_manager;
    $this->storage = $storage;
  }

  /**
   * Presents the embedded paragraphs creation form.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object.
   * @param \Drupal\editor\EditorInterface|null $editor
   *   The WYSIWYG editor.
   * @param \Drupal\embed\EmbedButtonInterface|null $embed_button
   *   The embed button.
   *
   * @return array
   *   A form array as expected by drupal_render().
   */
  public function addForm(Request $request, EditorInterface $editor = NULL, $entity_type_id, $field_id, EmbedButtonInterface $embed_button = NULL) {
    if (($return_html = $this->controllerCalledOutsideIframe($request))) {
      return $return_html;
    }

    //@todo maybe we dont need to receive $entity_type_id, $field_id from the plugin, maybe we can get from the container
    $form_state['paragraph_embed_dialog'] = [
      'editor' => $editor,
      'embed_button' => $embed_button,
      'entity_type_id' => $entity_type_id,
      'field_id' => $field_id,
    ];

//    $paragraphs = $this->entityTypeManager->getStorage('paragraph');
//    $form = $this->entityFormBuilder()
//      ->getForm($paragraphs, 'paragraph', $form_state);
//
//    return $form;

//    $node = $this->entityManager()->getStorage('node')->create(array(
//      'type' => 'article',
//    ));
//    $form = $this->entityFormBuilder()->getForm($node);

//    $par = $this->entityManager()->getStorage('paragraph')->create(array(
//      'type' => 'image',
//    ));
//    $form = $this->entityFormBuilder()->getForm($par);
//    return $form;

//    $comment = \Drupal::entityTypeManager()->getStorage('paragraph')->create([
//      'entity_type' => 'test',
//      'bundle' => 'test',
//      'entity_id' => 1,
//      'field_test' => 'comment',
//    ]);
//    $form = \Drupal::service('entity.form_builder')->getForm($comment);
//    return $form;

//    $par = $this->entityManager()->getStorage('paragraph')->create(array(
//      'type' => 'image',
//    ));
//    $form = $this->entityFormBuilder()->getForm($par, 'paragraphs', $form_state);
//    return $form;

//    $par = $this->entityManager()->getStorage('paragraph')->create(array(
//      'type' => 'image',
//    ));
//    $form = $this->entityFormBuilder()->getForm($par);
//    return $form;

//    $par = $this->entityManager()->getStorage('paragraph')->create(['article']);
//    $form = $this->entityFormBuilder()->getForm($par);
//    return $form;

//    $form = [];
//    $entity = \Drupal::service('entity_type.manager')->getStorage('node')->create(array(
//        'type' => 'test'
//      )
//    );
//    //Get the EntityFormDisplay (i.e. the default Form Display) of this content type
//    $entity_form_display = \Drupal::service('entity_type.manager')->getStorage('entity_form_display')
//      ->load('node.test.default');
//
//    //Get the body field widget and add it to the form
//    if ($widget = $entity_form_display->getRenderer('body')) { //Returns the widget class
//        $items = $entity->get('body'); //Returns the FieldItemsList interface
//        $items->filterEmptyItems();
//        $form['xbody'] = $widget->form($items, $form, $this->fs); //Builds the widget form and attach it to your form
//        $form['xbody']['#access'] = $items->access('edit');
//    }


//    $form_display = EntityFormDisplay::collectRenderDisplay($this->entity, $this->getOperation());
//    $this->setFormDisplay($form_display, $form_state);
//
//    $form['save'] = [
//      '#type' => 'submit',
//      '#value' => t('Save'),
//      '#weight' => 100,
//    ];
//
//    return $form;

//    $par = $this->entityManager()->getStorage('paragraph')->create(array(
//      'type' => 'image',
//    ));
//    $form = $this->entityFormBuilder()
//      ->getForm($par, 'paragraphs_entity_embed', $form_state);
//    return $form;

//    $node = $this->entityManager()->getStorage('node')->create(array(
//      'type' => 'article',
//    ));
//    //$form = $this->entityFormBuilder()->getForm($node, 'paragraphs_entity_embed');
//    $form = $this->entityFormBuilder()->getForm($node);
//    return $form;

//    $par = $this->entityManager->getStorage('embedded_paragraphs');
//    $form = $this->entityFormBuilder()
//      ->getForm($par, 'paragraphs_entity_embed', $form_state);

//    $form = \Drupal::formBuilder()->getForm(\Drupal\user\Form\UserLoginForm::class);
//    return $form;

//    $plugin_options = $this->container->get('plugin.manager.entity_embed.display')
//      ->getDefinitionOptionsForEntity('test');

//    $node = \Drupal::service('entity_type.manager')->getStorage('node')->create(array(
//        'type' => 'article'
//      )
//    );
//    $form = EntityFormDisplay::collectRenderDisplay($entity, $form_mode);
//    $form->buildForm($entity, $entity_form, $form_state);
//    return $form;

    $node = static::entityTypeManager()->getStorage('node')->create(array(
        'type' => $entity_type_id,
      )
    );
    $form = $this->entityFormBuilder()->getForm($node, 'paragraphs_entity_embed', $form_state);

    return $form;

//    $node = $this->entityManager()->getStorage('node')->create([
//      'type' => $entity_type_id,
//    ]);
//    $form = $this->entityFormBuilder()->getForm($node);
//    return $form;

    $par = $this->storage->create([]);
    $form = $this->entityFormBuilder()
      ->getForm($par, 'paragraphs_entity_embed', $form_state);

    return $form;

//    return $this->entityFormBuilder()
//      ->getForm($paragraphs, 'paragraphs_entity_embed', $form_state);
  }

  /**
   * Presents the embedded paragraphs update form.
   *
   * @param string $embedded_paragraphs_uuid
   *   The UUID of Embedded paragraphs we are going to edit via CKE modal form.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object.
   * @param \Drupal\editor\EditorInterface|null $editor
   *   The WYSIWYG editor.
   * @param \Drupal\embed\EmbedButtonInterface|null $embed_button
   *   The embed button.
   *
   * @return array
   *   A form array as expected by drupal_render().
   */
  public function editForm(
  $embedded_paragraphs_uuid,
  Request $request,
  EditorInterface $editor = NULL,
  EmbedButtonInterface $embed_button = NULL) {
    if (($return_html = $this->controllerCalledOutsideIframe($request))) {
      return $return_html;
    }

    $entity = $this->embeddedParagraphsStorage
      ->loadByProperties(['uuid' => $embedded_paragraphs_uuid]);
    $embedded_paragraph = current($entity);

    $form_state['paragraph_embed_dialog'] = [
      'editor' => $editor,
      'embed_button' => $embed_button,
    ];

    return $this->entityFormBuilder()
      ->getForm($embedded_paragraph, 'paragraphs_entity_embed', $form_state);
  }

  /**
   * Checks whether the current request is performed inside iframe.
   *
   * If its inside iframe nothing is returned, otherwise we return html markup
   * for showing iframe.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object.
   *
   * @return array|null
   *   Array of the response data or nothing if the controller is called inside
   *   iframe.
   */
  private function controllerCalledOutsideIframe(Request $request) {
    if (!$request->query->get('paragraphs_entity_embed_inside_iframe')) {
      $parsed_url = UrlHelper::parse($request->getRequestUri());
      if (isset($parsed_url['query']['_wrapper_format'])) {
        unset($parsed_url['query']['_wrapper_format']);
      }
      $parsed_url['query']['paragraphs_entity_embed_inside_iframe'] = 1;

      $iframe_source = $parsed_url['path'] . '?' . UrlHelper::buildQuery($parsed_url['query']);

      return [
        '#type' => 'html_tag',
        '#tag' => 'iframe',
        '#attributes' => [
          'src' => $iframe_source,
          'width' => '480',
          'height' => '450',
          'frameBorder' => 0,
        ],
        '#attached' => ['library' => ['editor/drupal.editor.dialog']],
      ];
    }
  }

  /**
   * Returns a page title.
   *
   * @param \Drupal\embed\EmbedButtonInterface|null $embed_button
   *   The embed button.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   Page title.
   */
  public function getEditTitle(EmbedButtonInterface $embed_button = NULL) {
    return  $this->t('Edit %title', ['%title' => $embed_button->label()]);
  }

  /**
   * Returns a page title.
   *
   * @param \Drupal\embed\EmbedButtonInterface|null $embed_button
   *   The embed button.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   Page title.
   */
  public function getAddTitle(EmbedButtonInterface $embed_button = NULL) {
    return  $this->t('Select %title to Embed', ['%title' => $embed_button->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#prefix'] = '<div id="geysir-modal-form">';
    $form['#suffix'] = '</div>';

    $routeParams = $form_state->getBuildInfo()['args'][0];
    $bundles = $form_state->getBuildInfo()['args'][1];

    $parent_entity_type = $routeParams['parent_entity_type'];
    $parent_entity_bundle = $routeParams['parent_entity_bundle'];
    $form_mode = 'default';
    $field = $routeParams['field'];

    $parent_field_settings = \Drupal::entityTypeManager()
      ->getStorage('entity_form_display')
      ->load($parent_entity_type . '.' . $parent_entity_bundle . '.' . $form_mode)
      ->getComponent($field);

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $bundles = $this->getAllowedBundles($bundles);
    $paragraphs_type_storage = \Drupal::entityTypeManager()->getStorage('paragraphs_type');

    $default_icon = drupal_get_path('module', 'geysir') . '/images/geysir-puzzle.svg';

    foreach ($bundles as $bundle => $label) {
      $icon_url = $default_icon;
      if ($paragraphs_type_storage->load($bundle)->getIconUrl()) {
        $icon_url = $paragraphs_type_storage->load($bundle)->getIconUrl();
      }
      $routeParams['bundle'] = $bundle;
      $form['description'][$bundle] = [
        '#type' => 'image_button',
        '#prefix' => '<div class="geysir-add-type">',
        '#suffix' => '<span>' . $label . '</span></div>',
        '#src' => $icon_url,
        '#value' => $label,
        '#ajax' => [
          'url' => Url::fromRoute(isset($routeParams['paragraph'])? 'geysir.modal.add_form': 'geysir.modal.add_form_first', $routeParams),
          'wrapper' => 'geysir-modal-form',
        ],
      ];
    }

    return $form;
  }

  /**
   * Returns a list of allowed Paragraph bundles to add.
   *
   * @param array $allowed_bundles
   *   An array with Paragraph bundles which are allowed to add.
   *
   * @return array
   *   Array with allowed Paragraph bundles.
   */
  protected function getAllowedBundles($allowed_bundles) {
    $bundles = \Drupal::service('entity_type.bundle.info')->getBundleInfo('paragraph');

    if (is_array($allowed_bundles) && count($allowed_bundles)) {
      // Preserve order of allowed bundles setting.
      $allowed_bundles_order = array_flip($allowed_bundles);
      // Only keep allowed bundles.
      $bundles = array_intersect_key(
        array_replace($allowed_bundles_order, $bundles),
        $allowed_bundles_order
      );
    }

    // Enrich bundles with their label.
    foreach ($bundles as $bundle => $props) {
      $label = empty($props['label']) ? ucfirst($bundle) : $props['label'];
      $bundles[$bundle] = $label;
    }

    return $bundles;
  }
}

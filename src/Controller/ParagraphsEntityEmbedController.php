<?php

namespace Drupal\paragraphs_entity_embed\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityInterface;
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
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    echo 1;
    return new static(
      $container->get('entity.manager')->getStorage('embedded_paragraphs')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function Xcreate(ContainerInterface $container) {
    echo 1;
    return new static(
      $container->get('entity.manager'),
      $container->get('entity_type.manager'),
      $container->get('entity.manager')->getStorage('embedded_paragraphs')
    );
  }

  /**
   * Constructs a EmbeddedParagraphs object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The custom embedded paragraphs storage.
   */
  public function __construct(EntityStorageInterface $storage) {
    $this->storage = $storage;
  }

  /**
   * Constructs a EmbeddedParagraphs object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The custom embedded paragraphs storage.
   */
  public function X__construct(EntityManagerInterface $entity_manager, EntityTypeManagerInterface $entity_type_manager, EntityStorageInterface $storage) {
    $this->entityManager = $entity_manager;
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
  public function addForm(
  Request $request,
  EditorInterface $editor = NULL,
  EmbedButtonInterface $embed_button = NULL) {
    if (($return_html = $this->controllerCalledOutsideIframe($request))) {
      return $return_html;
    }

    $form_state['editorParams'] = [
      'editor' => $editor,
      'embed_button' => $embed_button,
    ];
//    echo 'a';
//    $paragraphs = $this->entityTypeManager->getStorage('paragraph');
//    echo 'b';

//    $form = $this->entityFormBuilder()
//      ->getForm($paragraphs, 'paragraph', $form_state);
//    echo 'c';
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


//    $par = $this->entityManager->getStorage('embedded_paragraphs');
//    $form = $this->entityFormBuilder()
//      ->getForm($par, 'paragraphs_entity_embed', $form_state);

    echo 2;
    $par = $this->storage->create([]);
    echo 3;
    $form = $this->entityFormBuilder()
      ->getForm($par, 'paragraphs_entity_embed', $form_state);
    echo 4;
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

    $form_state['editorParams'] = [
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

}

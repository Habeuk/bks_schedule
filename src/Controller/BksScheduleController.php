<?php

namespace Drupal\bks_schedule\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Stephane888\DrupalUtility\HttpResponse;
use Stephane888\Debug\ExceptionExtractMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Drupal\booking_system_schedule\Services\ManageSchedule;
use Drupal\lesroidelareno\lesroidelareno;

/**
 * Returns responses for bks schedule routes.
 */
class BksScheduleController extends ControllerBase {
  /**
   *
   * @var ManageSchedule
   */
  protected $ManageSchedule;
  
  public function __construct(ManageSchedule $ManageSchedule) {
    $this->ManageSchedule = $ManageSchedule;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('booking_system_schedule.manager'));
  }
  
  /**
   * Builds the response to showing the Vue-js app.
   * On definit les urls pour l'initialisation de l'application.
   */
  public function build() {
    $booking_config_type_id = lesroidelareno::getCurrentDomainId();
    $urlSchedule = Url::fromRoute("bks_schedule.load_app", [
      'booking_config_type_id' => $booking_config_type_id
    ]);
    $build['content'] = [
      '#type' => 'html_tag',
      '#tag' => 'section',
      "#attributes" => [
        'id' => 'app',
        'data-url-schedule' => '/' . $urlSchedule->getInternalPath(),
        'class' => []
      ]
    ];
    $build['content']['#attached']['library'][] = 'bks_schedule/bks_schedule_app';
    return $build;
  }
  
  /**
   * Permet de charger les informations du planning.
   *
   * @param string $booking_config_type_id
   * @param string $date
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function loadConfigSchedule(Request $Request, $booking_config_type_id) {
    try {
      $datas = Json::decode($Request->getContent());
      if (!empty($datas['filtres'])) {
        // applies
      }
      $date_begin = "2023-08-21";
      $date_end = "2023-08-27";
      $configs = $this->ManageSchedule->loadCreneaux($booking_config_type_id, $date_begin, $date_end);
      return HttpResponse::response($configs);
    }
    catch (\Exception $e) {
      return HttpResponse::response(ExceptionExtractMessage::errorAll($e), 435);
    }
    catch (\Error $e) {
      return HttpResponse::response(ExceptionExtractMessage::errorAll($e), 435);
    }
  }
  
}

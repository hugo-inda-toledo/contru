<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

class BreadcrumbComponent extends Component
{
    /**
     * devuelve el nombre del controlador para el breadcrumb dependiendo del controlador
     * @param  string $controller controlador
     * @param  boolean $text_only si se necesita sólo texto
     * @return array | string breadcrumb
     * @author Diego De la Cruz B. <diego.delacruz@ideauno.cl>
     */
    public function getBreadcrumbByControllerName($controller = '', $text_only = '')
    {
        $text = '';
        $action = '';
        switch ($controller) {
            case 'Buildings':
                $text = __('Obras');
                $action = 'index';
                break;
            case 'Assists':
                $text = __('Asistencias');
                $action = 'index';
                break;
            case 'Bonuses':
                $text = __('Bonos');
                $action = 'index';
                break;
            case 'Budgets':
                $text = __('Presupuestos');
                $action = 'index';
                $controller = 'Buildings';
                break;
            case 'BudgetItems':
                $text = __('Partidas');
                $action = 'index';
                $controller = 'Buildings';
                break;
            case 'CompletedTasks':
                $text = __('Trabajos Realizados');
                $action = 'index';
                break;
            case 'Deals':
                $text = __('Tratos');
                $action = 'index';
                break;
            case 'Groups':
                $text = __('Perfiles de Usuario');
                $action = 'index';
                break;
            case 'Histories':
                $text = __('LOGS');
                $action = 'index';
                break;
            case 'PaymentStatements':
                $text = __('Estados de Pago');
                $action = 'index';
                break;
            case 'Progress':
                $text = __('Avances de Obra');
                $action = 'index';
                break;
            case 'Schedules':
                $text = __('Planificaciones');
                $action = 'index';
                break;
            case 'SalaryReports':
                $text = __('Reportes Remuneraciones');
                $action = 'index';
                break;
            case 'Users':
                $text = __('Usuarios');
                $action = 'index';
                break;
            case 'IconstruyeImports':
                $text = __('Importaciones Iconstruye');
                $action = 'index';
                break;
            case 'Spends':
                $text = __('Panel de control');
                $controller = 'buildings';
                $action = 'dashboard';
                break;
            default:
                $text = __('Inicio');
                $controller = 'users';
                $action = 'home';
                break;
        }
        if ($text_only) {
            return $text;
        } else {
        	$breadcrumb_vars = array();
        	$breadcrumb_vars['text'] = $text;
        	$breadcrumb_vars['controller'] = $controller;
        	$breadcrumb_vars['action'] = $action;
            return $breadcrumb_vars;
        }
    }

    /**
     * devuelve el nombre de la vista para el breadcrumb dependiendo del controlador
     * @param  string $action vista
     * @return string nombre de la vista + controller si es el caso
     * @author Diego De la Cruz B. <diego.delacruz@ideauno.cl>
     */
    public function getBreadcrumbByActionName($action = '')
    {
        $action_name = '';
        switch ($action) {
            case 'add' :
                $action_name = 'Agregar';
                break;
            case 'edit' :
                $action_name = 'Editar';
                break;
            case 'view' :
                $action_name = 'Ver Detalle';
                break;
            case 'index' :
                $action_name = 'Listado de';
                break;
            case 'omit_buildings' :
                $action_name = 'Listado Obras Ignoradas';
                break;
            case 'confirm_excel':
                $action_name = 'Confirmar Excel de Presupuesto';
                break;
            case 'disable_item':
                $action_name = 'Deshabilitar Partida de Presupuesto';
                break;
            case 'import_excel':
                $action_name = 'Importar Archivo Excel de Presupuesto';
                break;
            case 'item_param':
                $action_name = 'Editar Partida Adicional de Presupuesto';
                break;
            case 'add_permissions':
                $action_name = 'Configurar Permisos de Usuarios';
                break;
            case 'add_subcontracts':
                $action_name = 'Importar Subcontratos';
                break;
            case 'comment':
                $action_name = 'Agregar comentario';
                break;
            case 'updatePasswordAdmin':
                $action_name = 'Cambiar contraseña de usuario';
                break;
            case 'updatePassword':
                $action_name = 'Cambiar mi contraseña';
                break;
            case 'progress':
                $action_name = 'Agregar Avance Obra';
                break;
            case 'global_state':
                $action_name = 'Reporte Estado Actual Obra';
                break;
            case 'assist_month_detail':
                $action_name = 'Reporte Asistencia Mensual';
                break;
            case 'salaries_report':
                $action_name = 'Reporte Remuneraciones';
                break;
            case 'confirm':
                $action_name = 'Confirmar';
                break;
            case 'dashboard':
                $action_name = 'Panel de Control';
                break;
            case 'overview':
                $action_name = 'Máscara';
                break;
            case 'purchasedMaterialsDetails':
                $action_name = 'Ordenes de compra';
                break;
            case 'subcontractsDetails':
                $action_name = 'Subcontratos';
                break;
            case 'usedMaterialsDetails':
                $action_name = 'Materiales usados';
                break;
            case 'factMaterialsDetails':
                $action_name = 'Materiales facturados';
                break;

            default:
                $action_name = 'Funcionalidad';
                break;
        }
        if ($action == 'add' || $action == 'edit' || $action == 'view' || $action == 'index' || $action == 'confirm') {
            return $action_name . ' ' . $this->getBreadcrumbByControllerName($this->request->params['controller'], true);
        } else {
            return $action_name;
        }
    }

}

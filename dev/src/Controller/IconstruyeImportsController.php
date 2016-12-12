<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;

/**
 * IconstruyeImports Controller
 *
 * @property \App\Model\Table\IconstruyeImportsTable $IconstruyeImports */
class IconstruyeImportsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
         $this->paginate = [
             'contain' => ['UserUploaders']
         ];
        $this->set('iconstruyeImports', $this->paginate($this->IconstruyeImports));
        $this->set('_serialize', ['iconstruyeImports']);
    }

    /**
     * View method
     *
     * @param string|null $id Iconstruye Import id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null, $type = null)
    {
        if (!empty($id) && !empty($type) || $id != null && $type != null ) {
            if ($type == 'guide_exits') {
                $iconstruyeImport = $this->IconstruyeImports->get($id, [
                    'contain' => ['UserUploaders', 'GuideExits' => ['fields' => ['id', 'iconstruye_import_id']]]
                ]);
                $guideExit = $this->IconstruyeImports->GuideExits->find('all',[
                    'conditions' => ['iconstruye_import_id' => $id],
                    'contain' => ['BudgetItems', 'BudgetItems.Budgets.Buildings']
                ]);
                // debug($iconstruyeImport); die;
                $this->set('guideExit', $this->paginate($guideExit));
                $this->set('_serialize', ['guideExit']);

                $this->set('iconstruyeImport', $iconstruyeImport);
                $this->set('_serialize', ['iconstruyeImport']);
            } elseif ($type == 'subcontracts') {
                $iconstruyeImport = $this->IconstruyeImports->get($id, [
                    'contain' => ['UserUploaders', 'Subcontracts' => ['fields' => ['id', 'iconstruye_import_id']]]
                ]);
                $subcontracts = $this->IconstruyeImports->Subcontracts->find('all',[
                    'conditions' => ['iconstruye_import_id' => $id],
                    'contain' => ['BudgetItems', 'BudgetItems.Budgets.Buildings']
                ]);
                $this->set('iconstruyeImport', $iconstruyeImport);
                $this->set('subcontracts', $this->paginate($subcontracts));
                $this->set('_serialize', ['iconstruyeImport']);
            }
        } else {
            return $this->redirect(['action' => 'index']);
            $this->Flash->error('Ocurrió un error al buscar el registro. Por favor, inténtelo nuevamente');
        }
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $iconstruyeImport = $this->IconstruyeImports->newEntity();
        if ($this->request->is('post')) {
            $nueva_ruta = APP . 'upload_excel' . DS . 'temporal-' . date('Y-m-d_His') . '.lsx';
            if (!empty($this->request->data['file']['tmp_name'])) {
                //validar tipo archivo
                if (!$this->IconstruyeImports->validateFileType($this->request->data['file']['type'])) {
                    $this->Flash->warning('El archivo no corresponde a formato a excel. Por favor, inténtelo nuevamente');
                    // return $this->redirect($this->referer());
                } else {
                    if (move_uploaded_file($this->request->data['file']['tmp_name'], $nueva_ruta)) {
                        //validar excel
                        $this->request->data['file'] = $nueva_ruta;
                        $datos_excel = null;
                        $registroExiste = array();
                        $PartidaExiste = array();
                        $datos_excel = $this->IconstruyeImports->excel_guide_exits($nueva_ruta, 'guide_exits');
                        //$datos_excel['PartidaNoExiste'] = $PartidaNoExiste;
                        $session = $this->request->session();
                        $session->write('tmp.iconstruye.datos', $datos_excel ) ;
                        $session->write('tmp.iconstruye.file', $nueva_ruta);
                        return $this->redirect(['action' => 'confirm']);
                    } else {
                        $this->Flash->error('Ocurrió un error al procesar el archivo de importación. Por favor, inténtelo nuevamente');
                    }
                }
            } else {
                $this->Flash->error('Ocurrió un error al procesar el archivo de importación. Por favor, inténtelo nuevamente');
            }
        }
        //$files = $this->IconstruyeImports->Files->find('list', ['limit' => 200]);
        $userUploaders = $this->IconstruyeImports->UserUploaders->find('list', ['limit' => 200]);
        $this->set(compact('iconstruyeImport', 'files', 'userUploaders'));
        $this->set('_serialize', ['iconstruyeImport']);
    }

    public function add_subcontracts()
    {
        $iconstruyeImport = $this->IconstruyeImports->newEntity();
        if ($this->request->is('post')) {
            $nueva_ruta = APP . 'upload_excel' . DS . 'temporal-' . date('Y-m-d_His') . '.lsx';
            if (!empty($this->request->data['file']['tmp_name'])) {
                //validar tipo archivo
                if (!$this->IconstruyeImports->validateFileType($this->request->data['file']['type'])) {
                    $this->Flash->warning('El archivo no corresponde a formato a excel. Por favor, inténtelo nuevamente');
                    // return $this->redirect($this->referer());
                } else {
                    if (move_uploaded_file($this->request->data['file']['tmp_name'], $nueva_ruta)) {
                        //validar excel
                        $this->request->data['file'] = $nueva_ruta;
                        $datos_excel = null;
                        $registroExiste = array();
                        $PartidaExiste = array();
                        $datos_excel = $this->IconstruyeImports->excel_subcontracts($nueva_ruta, 'subcontracts');
                        $session = $this->request->session();
                        $session->write('tmp.iconstruye.datos', $datos_excel ) ;
                        $session->write('tmp.iconstruye.file', $nueva_ruta);
                        return $this->redirect(['action' => 'confirm']);
                    } else {
                        $this->Flash->error('Ocurrió un error al procesar el archivo de importación. Por favor, inténtelo nuevamente');
                    }
                }
            } else {
                $this->Flash->error('Ocurrió un error al procesar el archivo de importación. Por favor, inténtelo nuevamente');
            }
        }
        //$files = $this->IconstruyeImports->Files->find('list', ['limit' => 200]);
        $userUploaders = $this->IconstruyeImports->UserUploaders->find('list', ['limit' => 200]);
        $this->set(compact('iconstruyeImport', 'files', 'userUploaders'));
        $this->set('_serialize', ['iconstruyeImport']);
    }

    public function confirm()
    {
        $session = $this->request->session();
        if ($session->check('tmp.iconstruye')) {
            $datos_excel = $session->read('tmp.iconstruye.datos');
            $archivo = $session->read('tmp.iconstruye.file');
            //unset, por que si ... donde esta tu dios ahora.
            $this->set('excel', $datos_excel);
            $errores = (!empty($datos_excel['errores'])) ? $datos_excel['errores'] : array('No se encontraron errores.');
            $this->set('errores', $errores);
        }
        else {
            //no esta el excel guardado en sesion, redirect a add.
            return $this->redirect(['action' => 'add']);
        }
        $iconstruyeImport = $this->IconstruyeImports->newEntity();
        if ($this->request->is('post')) {
            //array con todas las keys con registros invalidos
            $errKeys = array_unique(array_column($datos_excel['errores'],'linea'));
            $totalRegistrosGuardar = count($datos_excel['registros']) - count($errKeys);
            $iconstruyeImport = $this->IconstruyeImports->newEntity();
            $iconstruyeImport->type = $datos_excel['type'];
            $iconstruyeImport->file_name = basename($archivo);
            $iconstruyeImport->transaction_lines = $totalRegistrosGuardar;
            $iconstruyeImport->user_uploader_id = $this->request->session()->read('Auth.User.id');
            $iconstruyeImport->file = $archivo;
            if ($totalRegistrosGuardar < 1) {
                $this->Flash->error('No se encontraron nuevos registros válidos para importar, por favor verifique sus datos.');
                return $this->redirect(['action' => 'add']);
            }
            if ($this->IconstruyeImports->save($iconstruyeImport)) {
                foreach($datos_excel['registros'] as $key => &$registro) {
                    // debug($r['date_system']); die;
                    if ($datos_excel['type'] == 'guide_exits') {
                        if (!in_array($key, $errKeys)) {
                            // registro valido
                            // debug('valido');
                            //parse fecha systema a datetime
                            $registro['date_system'] = strtotime($registro['date_system']);
                            // try {
                            //     $registro['date_system'] = Time::createFromFormat(
                            //         'm-d-y H:i', $registro['date_system']);
                            // } catch (Exception $e) {
                            //     $registro['date_system'] = strtotime($registro['date_system']);
                            // }

                            // if (!empty($registro['date_system'])) {
                            //     $registro['date_system'] = Time::createFromFormat(
                            //         'm-d-y H:i', $registro['date_system']);
                            // }
                            $guideExit = $this->IconstruyeImports->GuideExits->newEntity();
                            $guideExit = $this->IconstruyeImports->GuideExits->patchEntity($guideExit, $registro);
                            $guideExit->iconstruye_import_id = $iconstruyeImport->id;
                            // debug($guideExit); die;
                            if ($this->IconstruyeImports->GuideExits->save($guideExit)) {
                                $totalRegistrosGuardar--;
                            } else {
                                debug('fail guardar');
                                $this->IconstruyeImports->delete($iconstruyeImport->id);
                            }
                        } else {
                            //registro invalido
                        }
                    } elseif ($datos_excel['type'] == 'subcontracts') {
                        if (!in_array($key, $errKeys)) {
                            $subcontract = $this->IconstruyeImports->Subcontracts->newEntity();
                            $date = new \DateTime($registro['date']);
                            $subcontract->budget_item_id = $registro['budget_item_id'];
                            $subcontract->iconstruye_import_id = $iconstruyeImport->id;
                            $subcontract->subcontract_work_number = $registro['subcontract_work_number'];
                            $subcontract->rut = $registro['rut'];
                            $subcontract->name = $registro['name'];
                            $subcontract->description = $registro['description'];
                            $subcontract->unit_type = $registro['unit_type'];
                            $subcontract->currency = $registro['currency'];
                            $subcontract->currency_rate = $registro['currency_rate'];
                            $subcontract->amount = str_replace(',', '.', $registro['amount']);
                            $subcontract->price = str_replace('.', '', $registro['price']);
                            $subcontract->total = str_replace('.', '', $registro['total']);
                            $subcontract->partial_description = $registro['partial_description'];
                            $subcontract->partial_amount = str_replace(',', '.', $registro['partial_amount']);
                            $subcontract->partial_total = str_replace('.', '', $registro['partial_total']);
                            $subcontract->balance_due = str_replace('.', '', $registro['balance_due']);
                            $subcontract->payment_statement_total = str_replace('.', '', $registro['payment_statement_total']);
                            $subcontract->date = $date;
                            $subcontract->json = $registro['json'];
                            if ($this->IconstruyeImports->Subcontracts->save($subcontract)) {
                                $totalRegistrosGuardar--;
                            } else {
                                debug('fail guardar');
                                $this->IconstruyeImports->delete($iconstruyeImport->id);
                            }
                        }
                    }
                }
                if ($totalRegistrosGuardar == 0) {
                    $this->Flash->success('La Importación se ha realizado correctamente.');
                    return $this->redirect(['action' => 'index']);
                } else {

                }
            } else {
                $this->Flash->error('La Importación falló, por favor intente nuevamente.');
                return $this->redirect(['action' => 'add_subcontract']);
            }
        }
        //$files = $this->IconstruyeImports->Files->find('list', ['limit' => 200]);
        $userUploaders = $this->IconstruyeImports->UserUploaders->find('list', ['limit' => 200]);
        $this->set(compact('iconstruyeImport', 'files', 'userUploaders'));
        $this->set('_serialize', ['iconstruyeImport']);
    }
}

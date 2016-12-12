<?php
namespace App\Model\Table;

use App\Model\Entity\Currencies;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\Date;
use Cake\Console\ConsoleOutput;

/**
 * Currencies Model
 */
class CurrenciesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('currencies');
        $this->displayField('name');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsToMany('Budgets', [
            'through' => 'CurrenciesValues'
        ]);

        $this->hasMany('Valoresmonedas', [
            'className' => 'Valoresmonedas',
            'foreignKey' => 'currency_id',
            'sort' => ['Valoresmonedas.currency_date' => 'DESC']
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');
            
        $validator
            ->allowEmpty('name');
            
        $validator
            ->allowEmpty('description');
            
        $validator
            ->allowEmpty('amount');

        return $validator;
    }

    function updateCurrencies($date = null, $badge = null)
    {
        $new_currency = array();
        $currencies = $this->find('all', [
            'conditions' => ['Currencies.variable_value' => 1, 'Currencies.enabled' => 1]
        ])->toArray();

        foreach($currencies as $currency)
        {
            if($date == null)
            {
                $currency_values = $this->Valoresmonedas->find('all', [
                    'conditions' => ['Valoresmonedas.currency_id' => $currency->id],
                    'order' => ['Valoresmonedas.currency_date' => 'DESC'],
                ])->first();

                if(count($currency_values) > 0)
                {
                    $date_value = new Date($currency_values->currency_date);

                    if($date_value->format('Y-m-d') != date('Y-m-d'))
                    {

                        $json = file_get_contents('http://api.sbif.cl/api-sbifv3/recursos_api/'.$currency->sbif_api_keyword.'?apikey=cc75469cabb07767eb4f9c937c76df34685be610&formato=json');
                        $json = json_decode($json, true);

                        $currencyValueData = $this->Valoresmonedas->newEntity();

                        $currencyValueData->currency_id = $currency->id;

                        $new_date = new Date($json[$currency->plural_name][0]['Fecha']);
                        $currencyValueData->currency_date = $new_date->format('Y-m-d');

                        $new_value = strval($json[$currency->plural_name][0]['Valor']);
                        $new_value = str_replace('.', '', $new_value);
                        $new_value = str_replace(',', '.', $new_value);
                        $new_value = floatval($new_value);
                        $currencyValueData->currency_value = $new_value;
                        
                        $this->Valoresmonedas->save($currencyValueData);
                    }
                    else
                    {
                        $json = file_get_contents('http://api.sbif.cl/api-sbifv3/recursos_api/'.$currency->sbif_api_keyword.'?apikey=cc75469cabb07767eb4f9c937c76df34685be610&formato=json');
                        $json = json_decode($json, true);

                        $new_value = strval($json[$currency->plural_name][0]['Valor']);
                        $new_value = str_replace('.', '', $new_value);
                        $new_value = str_replace(',', '.', $new_value);
                        $new_value = floatval($new_value);

                        if($currency_values->currency_value != $new_value)
                        {
                            $currencyValueData = $this->Valoresmonedas->get($currency_values->id);

                            $currencyValueData->currency_value = $new_value;
                            $this->Valoresmonedas->save($currencyValueData);
                        }
                    }
                }
                else
                {
                    $json = file_get_contents('http://api.sbif.cl/api-sbifv3/recursos_api/'.$currency->sbif_api_keyword.'?apikey=cc75469cabb07767eb4f9c937c76df34685be610&formato=json');
                    $json = json_decode($json, true);

                    $currencyValueData = $this->Valoresmonedas->newEntity();

                    $currencyValueData->currency_id = $currency->id;

                    $new_date = new Date($json[$currency->plural_name][0]['Fecha']);
                    $currencyValueData->currency_date = $new_date->format('Y-m-d');

                    $new_value = strval($json[$currency->plural_name][0]['Valor']);
                    $new_value = str_replace('.', '', $new_value);
                    $new_value = str_replace(',', '.', $new_value);
                    $new_value = floatval($new_value);
                    $currencyValueData->currency_value = $new_value;

                    $this->Valoresmonedas->save($currencyValueData);
                }
            }
            else
            {
                if($badge == $currency->sbif_api_keyword)
                {
                    $specific_data = new Date($date);

                    for($x=0; $x < 3; $x++)
                    {
                        $json = file_get_contents('http://api.sbif.cl/api-sbifv3/recursos_api/'.$badge.'/'.$specific_data->year.'/'.$specific_data->month.'/dias/'.$specific_data->day.'?apikey=cc75469cabb07767eb4f9c937c76df34685be610&formato=json');
                        $json = json_decode($json, true);

                        if(isset($json[$currency->plural_name][0]['Fecha']))
                        {
                            break;
                        }
                        else
                        {
                            $specific_data->modify('-1 day');
                        }

                    }

                    $currencyValueData = $this->Valoresmonedas->newEntity();

                    $currencyValueData->currency_id = $currency->id;

                    $new_date = new Date($date);
                    $currencyValueData->currency_date = $new_date->format('Y-m-d');

                    $new_value = strval($json[$currency->plural_name][0]['Valor']);
                    $new_value = str_replace('.', '', $new_value);
                    $new_value = str_replace(',', '.', $new_value);
                    $new_value = floatval($new_value);
                    $currencyValueData->currency_value = $new_value;

                    $this->Valoresmonedas->save($currencyValueData);

                    $new_currency = $this->find('all')
                                    ->contain([
                                        'Valoresmonedas' => [
                                            'queryBuilder' => function($q) use ($date){
                                                return $q->where(['Valoresmonedas.currency_date'=> $date])->limit(1);
                                            }
                                        ]
                                    ])
                                    ->where(['Currencies.sbif_api_keyword' => $badge])
                                    ->first();
                    break;
                }
            }
        }

        if($new_currency != null)
        {
            return $new_currency;
        }
    }

    function getDayValue($date = null, $badge = null)
    {
        if($date != null && $badge != null)
        {
            $new_date = new Date($date);

            $currency_value = $this->find('all')
                                    ->contain([
                                        'Valoresmonedas' => [
                                            'queryBuilder' => function($q) use ($new_date){
                                                return $q->where(['Valoresmonedas.currency_date'=> $new_date->format('Y-m-d')])->limit(1);
                                            }
                                        ]
                                    ])
                                    ->where(['Currencies.sbif_api_keyword' => $badge])
                                    ->first();

            if($currency_value->valoresmonedas == null)
            {

                for($x=0; $x < 3; $x++)
                {
                    $new_date->modify('-1 day');

                    $currency_value = $this->find('all')
                                    ->contain([
                                        'Valoresmonedas' => [
                                            'queryBuilder' => function($q) use ($new_date){
                                                return $q->where(['Valoresmonedas.currency_date'=> $new_date->format('Y-m-d')])->limit(1);
                                            }
                                        ]
                                    ])
                                    ->where(['Currencies.sbif_api_keyword' => $badge])
                                    ->first();

                    if($currency_value->valoresmonedas != null)
                    {
                        break;
                    }
                }
            }

            return $currency_value->valoresmonedas[0]->currency_value;
        }
    }

    function transformValue($value = null, $original_badge = null, $new_badge = null, $date = null)
    {
        $new_value = 0;

        if($value != null && $original_badge != null && $new_badge != null && $date != null)
        {
            $new_currency = $this->find('all')
                            ->contain([
                                'Valoresmonedas' => [
                                    'queryBuilder' => function($q) use ($date){
                                        return $q->where(['Valoresmonedas.currency_date'=> $date])->limit(1);
                                    }
                                ]
                            ])
                            ->where(['Currencies.sbif_api_keyword' => $new_badge])
                            ->first();


            if(count($new_currency->valoresmonedas) == 0 && $new_badge != 'peso')
            {
                $new_currency = $this->updateCurrencies($date, $new_badge);
            }

            $original_currency = $this->find('all')
                                ->contain([
                                    'Valoresmonedas' => [
                                        'queryBuilder' => function($q) use ($date){
                                            return $q->where(['Valoresmonedas.currency_date'=> $date])->order(['Valoresmonedas.currency_date' => 'DESC'])->limit(3);
                                        }
                                    ]
                                ])
                                ->where(['Currencies.sbif_api_keyword' => $original_badge])
                                ->first();

            if($original_badge != 'peso')
            {
                if(count($original_currency->valoresmonedas) == 0)
                {
                    $original_currency = $this->updateCurrencies($date, $original_badge);
                }
            }

            /*if($value <= 0)
            {*/
                switch ($original_badge) 
                {
                    case 'peso':
                        $new_value = $value / $new_currency->valoresmonedas[0]->currency_value;
                        break;

                    case 'uf':
                        $new_value = ($value / $original_currency->valoresmonedas[0]->currency_value);
                        break;

                    case 'dolar':
                        $new_value = ($value / $original_currency->valoresmonedas[0]->currency_value);
                        break;

                    case 'euro':
                        $new_value = ($value / $original_currency->valoresmonedas[0]->currency_value);
                        break;

                    default:
                        if($new_currency->valoresmonedas != null)
                        {
                            $new_value = ($value * $original_currency->valoresmonedas[0]->currency_value) / $new_currency->valoresmonedas[0]->currency_value;
                        }
                        else
                        {
                            $new_value = ($value * $original_currency->valoresmonedas[0]->currency_value) / 1;
                        }
                        
                        break;
                }
            //}
        }

        return $new_value;
    }
}

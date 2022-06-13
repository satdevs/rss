<?php
// Baked at 2022.03.25. 14:01:36
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Forecasts Model
 *
 * @method \App\Model\Entity\Forecast newEmptyEntity()
 * @method \App\Model\Entity\Forecast newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Forecast[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Forecast get($primaryKey, $options = [])
 * @method \App\Model\Entity\Forecast findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Forecast patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Forecast[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Forecast|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Forecast saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Forecast[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Forecast[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Forecast[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Forecast[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ForecastsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('forecasts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create')
            ->add('id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->allowEmptyString('name');

        $validator
            ->nonNegativeInteger('year')
            ->allowEmptyString('year');

        $validator
            ->scalar('day')
            ->maxLength('day', 45)
            ->allowEmptyString('day');

        $validator
            ->scalar('d')
            ->maxLength('d', 20)
            ->allowEmptyString('d');

        $validator
            ->scalar('tmin')
            ->maxLength('tmin', 20)
            ->allowEmptyString('tmin');

        $validator
            ->scalar('tmax')
            ->maxLength('tmax', 20)
            ->allowEmptyString('tmax');

        $validator
            ->scalar('wx')
            ->maxLength('wx', 45)
            ->allowEmptyString('wx');

        $validator
            ->scalar('sr')
            ->maxLength('sr', 20)
            ->allowEmptyString('sr');

        $validator
            ->scalar('ss')
            ->maxLength('ss', 20)
            ->allowEmptyString('ss');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['id']), ['errorField' => 'id']);
        $rules->add($rules->isUnique(['year','day','d', 'tmin', 'tmax', 'wx']));
        //$rules->add($rules->isUnique(['date']));

        return $rules;
    }
}

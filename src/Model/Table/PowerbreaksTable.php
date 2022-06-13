<?php
// Baked at 2022.03.24. 10:00:56
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Powerbreaks Model
 *
 * @method \App\Model\Entity\Powerbreak newEmptyEntity()
 * @method \App\Model\Entity\Powerbreak newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Powerbreak[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Powerbreak get($primaryKey, $options = [])
 * @method \App\Model\Entity\Powerbreak findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Powerbreak patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Powerbreak[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Powerbreak|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Powerbreak saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Powerbreak[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Powerbreak[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Powerbreak[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Powerbreak[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PowerbreaksTable extends Table
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

        $this->setTable('powerbreaks');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
		$this->addBehavior('JeffAdmin.Datepicker');	// inserts only if there is date or time or datetime field
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
            ->maxLength('name', 45)
            ->allowEmptyString('name');

        $validator
            ->scalar('status')
            ->maxLength('status', 45)
            ->allowEmptyString('status');

        $validator
            ->scalar('street')
            ->maxLength('street', 45)
            ->allowEmptyString('street');

        $validator
            ->scalar('place')
            ->maxLength('place', 200)
            ->allowEmptyString('place');

        $validator
            ->scalar('house_from')
            ->maxLength('house_from', 20)
            ->allowEmptyString('house_from');

        $validator
            ->scalar('house_to')
            ->maxLength('house_to', 20)
            ->allowEmptyString('house_to');

        $validator
            ->scalar('date')
            ->maxLength('date', 20)
            ->allowEmptyString('date');

        $validator
            ->scalar('time_from')
            ->maxLength('time_from', 20)
            ->allowEmptyString('time_from');

        $validator
            ->scalar('time_to')
            ->maxLength('time_to', 20)
            ->allowEmptyString('time_to');

        $validator
            ->scalar('comment')
            ->maxLength('comment', 1000)
            ->allowEmptyString('comment');

        $validator
            ->scalar('comment2')
            ->maxLength('comment2', 1000)
            ->allowEmptyString('comment2');

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

        return $rules;
    }
	
}

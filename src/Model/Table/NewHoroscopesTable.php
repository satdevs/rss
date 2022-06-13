<?php
// Baked at 2022.03.24. 15:02:56
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * NewHoroscopes Model
 *
 * @method \App\Model\Entity\NewHoroscope newEmptyEntity()
 * @method \App\Model\Entity\NewHoroscope newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\NewHoroscope[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\NewHoroscope get($primaryKey, $options = [])
 * @method \App\Model\Entity\NewHoroscope findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\NewHoroscope patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\NewHoroscope[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\NewHoroscope|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NewHoroscope saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NewHoroscope[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\NewHoroscope[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\NewHoroscope[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\NewHoroscope[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class NewHoroscopesTable extends Table
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

        $this->setTable('new_horoscopes');
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
            ->maxLength('name', 20)
            ->allowEmptyString('name');

        $validator
            ->scalar('ckey')
            ->maxLength('ckey', 20)
            ->allowEmptyString('ckey');

        $validator
            ->integer('year')
            ->allowEmptyString('year');

        $validator
            ->integer('month')
            ->allowEmptyString('month');

        $validator
            ->integer('day')
            ->allowEmptyString('day');

        $validator
            ->scalar('date')
            ->maxLength('date', 30)
            ->allowEmptyString('date');

        $validator
            ->scalar('content')
            ->maxLength('content', 2000)
            ->allowEmptyString('content');

        $validator
            ->nonNegativeInteger('counter')
            ->allowEmptyString('counter');

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
		$rules->add($rules->isUnique(['year', 'date', 'ckey']));
		
        return $rules;
    }
	
}

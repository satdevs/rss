<?php
// Baked at 2022.03.11. 13:19:33
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CibCurrencies Model
 *
 * @method \App\Model\Entity\CibCurrency newEmptyEntity()
 * @method \App\Model\Entity\CibCurrency newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CibCurrency[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CibCurrency get($primaryKey, $options = [])
 * @method \App\Model\Entity\CibCurrency findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CibCurrency patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CibCurrency[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CibCurrency|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CibCurrency saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CibCurrency[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CibCurrency[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CibCurrency[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CibCurrency[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CibCurrenciesTable extends Table
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

        $this->setTable('cib_currencies');
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('description')
            ->maxLength('description', 100)
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->scalar('category')
            ->maxLength('category', 50)
            ->requirePresence('category', 'create')
            ->notEmptyString('category');

        $validator
            ->dateTime('pubDate')
            ->allowEmptyDateTime('pubDate');

        $validator
            ->scalar('guid')
            ->maxLength('guid', 1000)
            ->requirePresence('guid', 'create')
            ->notEmptyString('guid');

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
		$rules->add($rules->isUnique(['guid']));
        //$rules->add($rules->existsIn(['version_id'], 'Versions'), ['errorField' => 'version_id']);

        return $rules;
    }

}

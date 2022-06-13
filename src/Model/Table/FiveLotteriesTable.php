<?php
// Baked at 2022.03.11. 15:29:19
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FiveLotteries Model
 *
 * @method \App\Model\Entity\FiveLottery newEmptyEntity()
 * @method \App\Model\Entity\FiveLottery newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\FiveLottery[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FiveLottery get($primaryKey, $options = [])
 * @method \App\Model\Entity\FiveLottery findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\FiveLottery patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\FiveLottery[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FiveLottery|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FiveLottery saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FiveLottery[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\FiveLottery[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\FiveLottery[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\FiveLottery[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FiveLotteriesTable extends Table
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

        $this->setTable('five_lotteries');
        $this->setDisplayField('id');
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('year')
            ->maxLength('year', 6)
            ->requirePresence('year', 'create')
            ->notEmptyString('year');

        $validator
            ->scalar('week')
            ->maxLength('week', 2)
            ->requirePresence('week', 'create')
            ->notEmptyString('week');

        $validator
            ->date('pull_date')
            ->requirePresence('pull_date', 'create')
            ->notEmptyDate('pull_date');

        $validator
            ->scalar('results5')
            ->maxLength('results5', 20)
            ->requirePresence('results5', 'create')
            ->notEmptyString('results5');

        $validator
            ->scalar('results5price')
            ->maxLength('results5price', 20)
            ->requirePresence('results5price', 'create')
            ->notEmptyString('results5price');

        $validator
            ->scalar('results4')
            ->maxLength('results4', 20)
            ->requirePresence('results4', 'create')
            ->notEmptyString('results4');

        $validator
            ->scalar('results4price')
            ->maxLength('results4price', 20)
            ->requirePresence('results4price', 'create')
            ->notEmptyString('results4price');

        $validator
            ->scalar('results3')
            ->maxLength('results3', 20)
            ->requirePresence('results3', 'create')
            ->notEmptyString('results3');

        $validator
            ->scalar('results3price')
            ->maxLength('results3price', 20)
            ->requirePresence('results3price', 'create')
            ->notEmptyString('results3price');

        $validator
            ->scalar('results2')
            ->maxLength('results2', 20)
            ->requirePresence('results2', 'create')
            ->notEmptyString('results2');

        $validator
            ->scalar('results2price')
            ->maxLength('results2price', 20)
            ->requirePresence('results2price', 'create')
            ->notEmptyString('results2price');

        $validator
            ->scalar('number1')
            ->maxLength('number1', 20)
            ->requirePresence('number1', 'create')
            ->notEmptyString('number1');

        $validator
            ->scalar('number2')
            ->maxLength('number2', 20)
            ->requirePresence('number2', 'create')
            ->notEmptyString('number2');

        $validator
            ->scalar('number3')
            ->maxLength('number3', 20)
            ->requirePresence('number3', 'create')
            ->notEmptyString('number3');

        $validator
            ->scalar('number4')
            ->maxLength('number4', 20)
            ->requirePresence('number4', 'create')
            ->notEmptyString('number4');

        $validator
            ->scalar('number5')
            ->maxLength('number5', 20)
            ->requirePresence('number5', 'create')
            ->notEmptyString('number5');

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
		$rules->add($rules->isUnique(['year', 'week', 'pull_date']));
        //$rules->add($rules->existsIn(['version_id'], 'Versions'), ['errorField' => 'version_id']);

        return $rules;
    }
	
	
	
}

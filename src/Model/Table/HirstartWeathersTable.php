<?php
// Baked at 2022.03.09. 14:36:32
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * HirstartWeathers Model
 *
 * @method \App\Model\Entity\HirstartWeather newEmptyEntity()
 * @method \App\Model\Entity\HirstartWeather newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\HirstartWeather[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\HirstartWeather get($primaryKey, $options = [])
 * @method \App\Model\Entity\HirstartWeather findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\HirstartWeather patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\HirstartWeather[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\HirstartWeather|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\HirstartWeather saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\HirstartWeather[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\HirstartWeather[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\HirstartWeather[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\HirstartWeather[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class HirstartWeathersTable extends Table
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

        $this->setTable('hirstart_weathers');
        $this->setDisplayField('title');
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
            ->scalar('title')
            ->maxLength('title', 1000)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->dateTime('pubdate')
            ->requirePresence('pubdate', 'create')
            ->notEmptyDateTime('pubdate');

        $validator
            ->scalar('category')
            ->maxLength('category', 50)
            ->requirePresence('category', 'create')
            ->notEmptyString('category');

/*
        $validator
            ->scalar('guid')
            ->maxLength('guid', 32)
            ->requirePresence('guid', 'create')
            ->notEmptyString('guid');
*/
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

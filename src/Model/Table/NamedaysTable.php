<?php
// Baked at 2022.03.17. 09:06:40
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Namedays Model
 *
 * @method \App\Model\Entity\Nameday newEmptyEntity()
 * @method \App\Model\Entity\Nameday newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Nameday[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Nameday get($primaryKey, $options = [])
 * @method \App\Model\Entity\Nameday findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Nameday patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Nameday[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Nameday|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Nameday saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Nameday[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Nameday[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Nameday[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Nameday[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class NamedaysTable extends Table
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

        $this->setTable('namedays');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
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
            ->requirePresence('month', 'create')
            ->notEmptyString('month');

        $validator
            ->requirePresence('day', 'create')
            ->notEmptyString('day');

        $validator
            ->scalar('name')
            ->maxLength('name', 20)
            ->allowEmptyString('name');

        $validator
            ->scalar('days')
            ->maxLength('days', 250)
            ->requirePresence('days', 'create')
            ->notEmptyString('days');

        $validator
            ->scalar('gender')
            ->maxLength('gender', 5)
            ->requirePresence('gender', 'create')
            ->notEmptyString('gender');

        $validator
            ->scalar('meaning')
            ->requirePresence('meaning', 'create')
            ->notEmptyString('meaning');

        $validator
            ->scalar('source')
            ->requirePresence('source', 'create')
            ->notEmptyString('source');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('details')
            ->requirePresence('details', 'create')
            ->notEmptyString('details');

        $validator
            ->scalar('nicknames')
            ->maxLength('nicknames', 250)
            ->requirePresence('nicknames', 'create')
            ->notEmptyString('nicknames');

        return $validator;
    }
}

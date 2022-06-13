<?php
// Baked at 2022.03.17. 09:48:09
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Curiosities Model
 *
 * @method \App\Model\Entity\Curiosity newEmptyEntity()
 * @method \App\Model\Entity\Curiosity newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Curiosity[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Curiosity get($primaryKey, $options = [])
 * @method \App\Model\Entity\Curiosity findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Curiosity patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Curiosity[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Curiosity|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Curiosity saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Curiosity[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Curiosity[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Curiosity[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Curiosity[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CuriositiesTable extends Table
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

        $this->setTable('curiosities');
        $this->setDisplayField('id');
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
            ->scalar('body')
            ->requirePresence('body', 'create')
            ->notEmptyString('body');

        $validator
            ->boolean('current')
            ->requirePresence('current', 'create')
            ->notEmptyString('current');

        $validator
            ->nonNegativeInteger('counter')
            ->requirePresence('counter', 'create')
            ->notEmptyString('counter');

        return $validator;
    }
}

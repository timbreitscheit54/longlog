<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var $queryClassName string queryParams class name */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

$columns = [];
foreach ($tableSchema->columns as $column) {
    $columns[] = "'$column->name'";
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @see <?= $modelFullClassName . "\n" ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{
    /**
     * Add general conditions
     *
     * @return $this
     */
    /*public function general()
    {
        $tableName = <?= $modelFullClassName ?>::tableName();

        $this->select([
        <?php foreach ($tableSchema->columns as $column): ?>
    "$tableName.<?= $column->name ?>",
        <?php endforeach ?>]);

        $this->orderBy(["$tableName.created_at" => SORT_DESC]);

        return $this;
    }*/

    /**
     * Filter only active data
     *
     * @return $this
     */
    /*public function active()
    {
        return $this->andWhere(['<?= $tableName ?>.status' => <?= $modelFullClassName ?>::STATUS_ACTIVE]);
    }*/

    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return \yii\db\BatchQueryResult|<?= $modelFullClassName ?>[]|array
     */
    public function each($batchSize = 100, $db = null)
    {
        return parent::each($batchSize, $db);
    }
}

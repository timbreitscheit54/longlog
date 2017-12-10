<?php

namespace frontend\models;

use common\helpers\enum\ProjectUserRole;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Project;
use yii\helpers\ArrayHelper;

/**
 * ProjectSearch represents the model behind the search form about `common\models\Project`.
 */
class ProjectSearch extends Project
{
    public $myRole;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'token', 'createdAt'], 'safe'],
            [['myRole'], 'in', 'range' => ProjectUserRole::getKeys()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Project::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['createdAt' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->active();
        // Join current logged in user project's roles
        $query->joinWith(['currentProjectUser']);

        // By default show all projects with any role
        $roles = [ProjectUserRole::VIEWER, ProjectUserRole::ADMIN];
        // Filter by selected role
        if ($this->myRole) {
            $roles = $this->myRole;
        }
        $query->andWhere(['currentProjectUser.role' => $roles]);

        // Filter like by name and token
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'token', $this->token]);

        return $dataProvider;
    }
}

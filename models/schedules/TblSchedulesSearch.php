<?php

namespace app\models\schedules;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\schedules\TblSchedules;

/**
 * TblSchedulesSearch represents the model behind the search form of `app\models\schedules\TblSchedules`.
 */
class TblSchedulesSearch extends TblSchedules
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'zones', 'sounds', 'schedules', 'desc'], 'safe'],
            [['enable'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = TblSchedules::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'enable' => $this->enable,
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'zones', $this->zones])
            ->andFilterWhere(['ilike', 'sounds', $this->sounds])
            ->andFilterWhere(['ilike', 'schedules', $this->schedules])
            ->andFilterWhere(['ilike', 'desc', $this->desc]);

        return $dataProvider;
    }
}

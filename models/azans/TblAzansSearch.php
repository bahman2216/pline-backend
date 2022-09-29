<?php

namespace app\models\azans;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\azans\TblAzans;

/**
 * TblAzansSearch represents the model behind the search form of `app\models\azans\TblAzans`.
 */
class TblAzansSearch extends TblAzans
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sound1', 'befor_sound1', 'after_sound1', 'volume1', 'sound2', 'befor_sound2', 'after_sound2', 'volume2', 'sound3', 'befor_sound3', 'after_sound3', 'volume3'], 'integer'],
            [['date', 'time1', 'zones1', 'time2', 'zones2', 'time3', 'zones3', 'desc'], 'safe'],
            [['enable1', 'enable2', 'enable3'], 'boolean'],
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
        $query = TblAzans::find();

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
            'sound1' => $this->sound1,
            'befor_sound1' => $this->befor_sound1,
            'after_sound1' => $this->after_sound1,
            'volume1' => $this->volume1,
            'enable1' => $this->enable1,
            'sound2' => $this->sound2,
            'befor_sound2' => $this->befor_sound2,
            'after_sound2' => $this->after_sound2,
            'volume2' => $this->volume2,
            'enable2' => $this->enable2,
            'sound3' => $this->sound3,
            'befor_sound3' => $this->befor_sound3,
            'after_sound3' => $this->after_sound3,
            'volume3' => $this->volume3,
            'enable3' => $this->enable3,
        ]);

        $query->andFilterWhere(['ilike', 'date', $this->date])
            ->andFilterWhere(['ilike', 'time1', $this->time1])
            ->andFilterWhere(['ilike', 'zones1', $this->zones1])
            ->andFilterWhere(['ilike', 'time2', $this->time2])
            ->andFilterWhere(['ilike', 'zones2', $this->zones2])
            ->andFilterWhere(['ilike', 'time3', $this->time3])
            ->andFilterWhere(['ilike', 'zones3', $this->zones3])
            ->andFilterWhere(['ilike', 'desc', $this->desc]);

        return $dataProvider;
    }
}

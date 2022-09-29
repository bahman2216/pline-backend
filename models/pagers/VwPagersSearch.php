<?php

namespace app\models\pagers;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VwPagersSearch represents the model behind the search form of `app\models\pagers\TblPagers`.
 */
class VwPagersSearch extends VwPagers
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type_pager', 'zone_id'], 'integer'],
            [['username', 'password', 'desc', 'zone_name'], 'safe'],
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
        $query = VwPagers::find();

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
            'type_pager' => $this->type_pager,
            'zone_id' => $this->zone_id,
            'enable' => $this->enable,
        ]);

        $query->andFilterWhere(['ilike', 'username', $this->username])
            ->andFilterWhere(['ilike', 'password', $this->password])
            ->andFilterWhere(['ilike', 'zone_name', $this->zone_name])
            ->andFilterWhere(['ilike', 'desc', $this->desc]);

        return $dataProvider;
    }
}

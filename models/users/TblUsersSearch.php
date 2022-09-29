<?php

namespace app\models\users;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\users\TblUsers;

/**
 * TblUsersSearch represents the model behind the search form of `app\models\users\TblUsers`.
 */
class TblUsersSearch extends TblUsers
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['username', 'password', 'authKey', 'accessToken', 'id_number', 'f_name', 'l_name', 'departeman', 'desc'], 'safe'],
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
        $query = TblUsers::find();

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

        $query->andFilterWhere(['ilike', 'username', $this->username])
            ->andFilterWhere(['ilike', 'password', $this->password])
            ->andFilterWhere(['ilike', 'authKey', $this->authKey])
            ->andFilterWhere(['ilike', 'accessToken', $this->accessToken])
            ->andFilterWhere(['ilike', 'id_number', $this->id_number])
            ->andFilterWhere(['ilike', 'f_name', $this->f_name])
            ->andFilterWhere(['ilike', 'l_name', $this->l_name])
            ->andFilterWhere(['ilike', 'departeman', $this->departeman])
            ->andFilterWhere(['ilike', 'desc', $this->desc]);

        return $dataProvider;
    }
}
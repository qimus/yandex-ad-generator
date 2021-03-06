<?php

namespace app\models\search;

use app\lib\api\yandex\direct\Connection;
use app\lib\api\yandex\direct\resources\CampaignResource;
use app\lib\provider\ActiveCampaignProvider;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\YandexCampaign;
use yii\db\ActiveQuery;

/**
 * YandexCampaignSearch represents the model behind the search form about `app\models\YandexCampaign`.
 */
class YandexCampaignSearch extends YandexCampaign
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'shop_id', 'brand_id', 'yandex_id', 'products_count', 'campaign_template_id'], 'integer'],
            [['title', 'shop.name'], 'safe'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['shop.name', 'campaignTemplate.title']);
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
        $query = YandexCampaign::find();

        $query->joinWith(['shop', 'campaignTemplate']);

        $dataProvider = new ActiveCampaignProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['shop.name'] = [
            'asc' => ['shop.name' => SORT_ASC],
            'desc' => ['shop.name' => SORT_DESC]
        ];

        $dataProvider->sort->attributes['campaignTemplate.title'] = [
            'asc' => ['campaign_template.title' => SORT_ASC],
            'desc' => ['campaign_template.title' => SORT_DESC]
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            self::fullColumn('shop_id') => $this->shop_id,
            self::fullColumn('brand_id') => $this->brand_id,
            'yandex_id' => $this->yandex_id,
            'products_count' => $this->products_count,
            'campaign_template_id' => $this->campaign_template_id
        ]);

        $query->andFilterWhere(['like', self::fullColumn('title'), $this->title]);
        $query->andFilterWhere(['like', 'shop.name', $this->getAttribute('shop.name')]);

        return $dataProvider;
    }
}

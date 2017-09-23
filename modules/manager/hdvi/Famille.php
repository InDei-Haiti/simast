<?php

namespace app\models;

use Yii;
use app\models\Vulnerability;
use yii\db\Query;

/**
 * This is the model class for table "famille".
 *
 * @property string $URI
 * @property string $ID_HH_A3_FINAL
 * @property string $ID_HH_ADM_KOMIN
 * @property string $ID_HH_ADM_SEK_KOM
 * @property integer $ID_HH_MILIEU_RESID
 * @property integer $ID_HH_A5_1
 * @property string $ID_HH_A5_2
 * @property string $ID_HH_A5_3
 * @property integer $ID_HH_A6
 * @property string $ID_HH_A8
 * @property integer $ID_HH_A8_3
 * @property integer $ID_HH_A8_4
 * @property integer $ID_HH_A8_5
 * @property string $ID_HH_A9
 * @property string $ID_HH_A10_ACC
 * @property string $ID_HH_A10_ALT
 * @property string $ID_HH_A10_LAT
 * @property string $ID_HH_A10_LNG
 * @property integer $ID_HH_NO_NON_RESYDAN
 * @property integer $ID_HH_NO_RESYDAN
 * @property integer $ID_HH_MANM_HH_TOTAL
 * @property integer $MANM_HH1_D6_1
 * @property integer $MANM_HH1_MALAD_QUE_CRON
 * @property integer $MANM_HH1_MALAD_NO_CRON
 * @property integer $MANM_HH1_MALAD_QUE_MWA
 * @property integer $MANM_HH1_MALAD_NO_MWA
 * @property integer $MANM_HH1_NO_MOURI
 * @property integer $BIENS_HH_F1
 * @property integer $BIENS_HH_F2
 * @property integer $BIENS_HH_F3_F3_1
 * @property integer $BIENS_HH_F3_F3_2
 * @property integer $BIENS_HH_F3_F3_3
 * @property integer $BIENS_HH_F3_F3_4
 * @property integer $BIENS_HH_F3_F3_5
 * @property integer $BIENS_HH_F3_F3_6
 * @property integer $BIENS_HH_F3_F3_7
 * @property integer $BIENS_HH_F3_F3_8
 * @property integer $BIENS_HH_F3_F3_9
 * @property integer $BIENS_HH_F3_F3_10
 * @property integer $BIENS_HH_F3_F3_11
 * @property integer $BIENS_HH_F3_F3_12
 * @property integer $BIENS_HH_F3_F3_13
 * @property integer $BIENS_HH_F3_F3_14
 * @property integer $BIENS_HH_F3_F3_15
 * @property integer $BIENS_HH_F3_F3_16
 * @property integer $BIENS_HH_F3_F3_17
 * @property integer $BIENS_HH_F3_F3_18
 * @property integer $BIENS_HH_F4
 * @property integer $BIENS_HH_F5_F5_1
 * @property integer $BIENS_HH_F5_F5_2
 * @property integer $BIENS_HH_F5_F5_3
 * @property integer $BIENS_HH_F5_F5_4
 * @property integer $BIENS_HH_F5_F5_5
 * @property integer $BIENS_HH_F5_F5_6
 * @property integer $BIENS_HH_F5_F5_7
 * @property integer $BIENS_HH_F6
 * @property integer $BIENS_HH_F7
 * @property integer $BIENS_HH_F8_1
 * @property integer $BIENS_HH_F8_2
 * @property integer $BIENS_HH_F9
 * @property integer $BIENS_HH_F9_1
 * @property integer $BIENS_HH_F10
 * @property integer $BIENS_HH_F11
 * @property integer $BIENS_HH_F12
 * @property integer $BIENS_HH_F12_0
 * @property integer $BIENS_HH_F13
 * @property integer $BIENS_HH_F14
 * @property integer $BIENS_HH_F15
 * @property integer $BIENS_HH_F15_0
 * @property integer $BIENS_HH_F16
 * @property integer $BIENS_HH_F17
 * @property integer $BIENS_HH_F18
 * @property integer $BIENS_HH_F19
 * @property integer $BIENS_HH_F20
 * @property integer $BIENS_HH_G1
 * @property integer $BIENS_HH_G2
 * @property integer $SEKALIMHHSEKALIMHH1_H1
 * @property integer $SEKALIMHHSEKALIMHH1_H2
 * @property integer $SEKALIMHHSEKALIMHH1_H3
 * @property integer $SEKALIMHHSEKALIMHH1_H4
 * @property integer $SEKALIMHHSEKALIMHH1_H5
 * @property integer $SEKALIMHHSEKALIMHH1_H6
 * @property integer $SEKALIMHHSEKALIMHH1_H7
 * @property integer $SEKALIMHHSEKALIMHH1_H8
 * @property integer $SEKALIMHHSEKALIMHH1_H9
 * @property integer $SEKALIMHHSEKALIMHH1_H10
 * @property integer $SEKALIMHHSEKALIMHH1_H11
 * @property integer $SEKALIMHHSEKALIMHH1_H12
 * @property integer $SEKALIMHHSEKALIMHH1_H13
 * @property integer $SEKALIMHHSEKALIMHH1_H14
 * @property integer $SEKALIMHHSEKALIMHH1_H15
 * @property integer $SEKALIMHHSEKALIMHH1_H16
 * @property integer $SEKALIMHHSEKALIMHH1_H17
 * @property integer $SEKALIMHHSEKALIMHH1_H18
 * @property integer $SEKALIMHHSEKALIMHH1_H19
 * @property integer $SEKALIMHHSEKALIMHH1_H20
 * @property integer $SEKALIMHHSEKALIMHH1_H21
 * @property integer $SEKALIMHHSEKALIMHH1_H22
 * @property integer $SEKALIMHHSEKALIMHH1_H23
 * @property integer $SEKALIMHHSEKALIMHH2_H1_B
 * @property integer $SEKALIMHHSEKALIMHH2_H2_B
 * @property integer $SEKALIMHHSEKALIMHH2_H3_B
 * @property integer $SEKALIMHHSEKALIMHH2_H4_B
 * @property integer $SEKALIMHHSEKALIMHH2_H5_B
 * @property integer $SEKALIMHHSEKALIMHH2_H6_B
 * @property integer $SEKALIMHHSEKALIMHH2_H7_B
 * @property integer $SEKALIMHHSEKALIMHH2_H8_B
 * @property integer $SEKALIMHHSEKALIMHH2_H9_B
 * @property integer $SEKALIMHHSEKALIMHH2_H10_B
 * @property integer $SEKALIMHHSEKALIMHH2_H11_B
 * @property integer $SEKALIMHHSEKALIMHH2_H12_B
 * @property integer $SEKALIMHHSEKALIMHH2_H13_B
 * @property integer $SEKALIMHHSEKALIMHH2_H14_B
 * @property integer $SEKALIMHHSEKALIMHH2_H15_B
 * @property integer $SEKALIMHHSEKALIMHH2_H16_B
 * @property integer $SEKALIMHHSEKALIMHH2_H17_B
 * @property integer $SEKALIMHHSEKALIMHH2_H18_B
 * @property integer $SEKALIMHHSEKALIMHH2_H19_B
 * @property integer $SEKALIMHHSEKALIMHH2_H20_B
 * @property integer $SEKALIMHHSEKALIMHH2_H21_B
 * @property integer $SEKALIMHHSEKALIMHH2_H22_B
 * @property integer $SEKALIMHHSEKALIMHH2_H23_B
 * @property integer $SEKALIMHHSEKALIMHH3_H24
 * @property integer $SEKALIMHHSEKALIMHH3_H25
 * @property integer $SEKALIMHHSEKALIMHH3_H26
 * @property integer $H24
 * @property integer $H25
 * @property integer $H26
 * @property integer $SEKALIMHHSEKALIMHH3_H27
 * @property integer $SEKALIMHHSEKALIMHH3_H28
 * @property integer $SEKALIMHHSEKALIMHH3_H29
 * @property integer $SEKALIMHHSEKALIMHH3_H30
 * @property integer $SEKALIMHHSEKALIMHH3_H31
 * @property integer $SEKALIMHHSEKALIMHH3_H32
 * @property integer $SEKALIMHHSEKALIMHH3_H33
 * @property integer $SEKALIMHHSEKALIMHH3_H34
 * @property integer $SEKALIMHHSEKALIMHH3_H35
 * @property integer $SEKALIMHHSEKALIMHH3_H36
 * @property integer $SEKALIMHHSEKALIMHH3_H37
 * @property integer $SEKALIMHHSEKALIMHH3_H38
 * @property integer $SEKALIMHHSEKALIMHH3_H39
 * @property integer $SEKALIMHHSEKALIMHH3_H40
 * @property integer $SEKALIMHHSEKALIMHH3_H41
 * @property integer $SEKALIMHHSEKALIMHH3_H42
 * @property integer $SEKALIMHHSEKALIMHH3_H43
 * @property integer $SEKALIMHHSEKALIMHH3_H44
 * @property integer $SEKALIMHHSEKALIMHH3_H45
 * @property integer $SEKALIMHHSEKALIMHH3_H46
 * @property integer $SEKALIMHHSEKALIMHH4_H47
 * @property integer $SEKALIMHHSEKALIMHH4_H48
 * @property integer $SEKALIMHHSEKALIMHH4_H49
 * @property integer $SEKALIMHHSEKALIMHH4_H50
 * @property integer $SEKALIMHHSEKALIMHH4_H51
 * @property integer $SEKALIMHHSEKALIMHH4_H52
 * @property integer $SEKALIMHHSEKALIMHH4_H53
 * @property integer $SEKALIMHHSEKALIMHH4_H54
 * @property integer $SEKALIMHHSEKALIMHH4_H55
 * @property integer $SEKALIMHHSEKALIMHH4_H56
 * @property integer $SEKALIMHHSEKALIMHH4_H57
 * @property integer $SEKALIMHHSEKALIMHH4_H58
 * @property integer $SEKALIMHHSEKALIMHH4_H59
 * @property integer $SEKALIMHHSEKALIMHH4_H60
 * @property integer $SEKALIMHHSEKALIMHH4_H61
 * @property integer $SEKALIMHHSEKALIMHH4_H62
 * @property integer $SEKALIMHHSEKALIMHH4_H63
 * @property integer $SEKALIMHHSEKALIMHH4_H64
 * @property integer $SEKALIMHHSEKALIMHH4_H65
 * @property integer $SEKALIMHHSEKALIMHH4_H66
 * @property integer $SEKALIMHHSEKALIMHH4_H67
 * @property integer $SEKALIMHHSEKALIMHH4_H68
 * @property integer $SEKALIMHHSEKALIMHH4_H69
 * @property integer $SEVIS_KONPOTMAN_HH_I1
 * @property integer $SEVIS_KONPOTMAN_HH_I2
 * @property integer $SEVIS_KONPOTMAN_HH_I3
 * @property integer $SEVIS_KONPOTMAN_HH_I4
 * @property integer $SEVIS_KONPOTMAN_HH_I5_1
 * @property integer $SEVIS_KONPOTMAN_HH_I5_2
 * @property integer $SEVIS_KONPOTMAN_HH_I6
 * @property integer $SEVIS_KONPOTMAN_HH_I7
 * @property integer $SEVIS_KONPOTMAN_HH_I8
 *
 * @property Chroniques[] $chroniques
 * @property Membre[] $membres
 * @property Recents[] $recents
 */
class Famille extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'famille';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['URI'], 'required'],
            [['ID_HH_MILIEU_RESID', 'ID_HH_A5_1', 'ID_HH_A6', 'ID_HH_A8_3', 'ID_HH_A8_4', 'ID_HH_A8_5', 'ID_HH_NO_NON_RESYDAN', 'ID_HH_NO_RESYDAN', 'ID_HH_MANM_HH_TOTAL', 'MANM_HH1_D6_1', 'MANM_HH1_MALAD_QUE_CRON', 'MANM_HH1_MALAD_NO_CRON', 'MANM_HH1_MALAD_QUE_MWA', 'MANM_HH1_MALAD_NO_MWA', 'MANM_HH1_NO_MOURI', 'BIENS_HH_F1', 'BIENS_HH_F2', 'BIENS_HH_F3_F3_1', 'BIENS_HH_F3_F3_2', 'BIENS_HH_F3_F3_3', 'BIENS_HH_F3_F3_4', 'BIENS_HH_F3_F3_5', 'BIENS_HH_F3_F3_6', 'BIENS_HH_F3_F3_7', 'BIENS_HH_F3_F3_8', 'BIENS_HH_F3_F3_9', 'BIENS_HH_F3_F3_10', 'BIENS_HH_F3_F3_11', 'BIENS_HH_F3_F3_12', 'BIENS_HH_F3_F3_13', 'BIENS_HH_F3_F3_14', 'BIENS_HH_F3_F3_15', 'BIENS_HH_F3_F3_16', 'BIENS_HH_F3_F3_17', 'BIENS_HH_F3_F3_18', 'BIENS_HH_F4', 'BIENS_HH_F5_F5_1', 'BIENS_HH_F5_F5_2', 'BIENS_HH_F5_F5_3', 'BIENS_HH_F5_F5_4', 'BIENS_HH_F5_F5_5', 'BIENS_HH_F5_F5_6', 'BIENS_HH_F5_F5_7', 'BIENS_HH_F6', 'BIENS_HH_F7', 'BIENS_HH_F8_1', 'BIENS_HH_F8_2', 'BIENS_HH_F9', 'BIENS_HH_F9_1', 'BIENS_HH_F10', 'BIENS_HH_F11', 'BIENS_HH_F12', 'BIENS_HH_F12_0', 'BIENS_HH_F13', 'BIENS_HH_F14', 'BIENS_HH_F15', 'BIENS_HH_F15_0', 'BIENS_HH_F16', 'BIENS_HH_F17', 'BIENS_HH_F18', 'BIENS_HH_F19', 'BIENS_HH_F20', 'BIENS_HH_G1', 'BIENS_HH_G2', 'SEKALIMHHSEKALIMHH1_H1', 'SEKALIMHHSEKALIMHH1_H2', 'SEKALIMHHSEKALIMHH1_H3', 'SEKALIMHHSEKALIMHH1_H4', 'SEKALIMHHSEKALIMHH1_H5', 'SEKALIMHHSEKALIMHH1_H6', 'SEKALIMHHSEKALIMHH1_H7', 'SEKALIMHHSEKALIMHH1_H8', 'SEKALIMHHSEKALIMHH1_H9', 'SEKALIMHHSEKALIMHH1_H10', 'SEKALIMHHSEKALIMHH1_H11', 'SEKALIMHHSEKALIMHH1_H12', 'SEKALIMHHSEKALIMHH1_H13', 'SEKALIMHHSEKALIMHH1_H14', 'SEKALIMHHSEKALIMHH1_H15', 'SEKALIMHHSEKALIMHH1_H16', 'SEKALIMHHSEKALIMHH1_H17', 'SEKALIMHHSEKALIMHH1_H18', 'SEKALIMHHSEKALIMHH1_H19', 'SEKALIMHHSEKALIMHH1_H20', 'SEKALIMHHSEKALIMHH1_H21', 'SEKALIMHHSEKALIMHH1_H22', 'SEKALIMHHSEKALIMHH1_H23', 'SEKALIMHHSEKALIMHH2_H1_B', 'SEKALIMHHSEKALIMHH2_H2_B', 'SEKALIMHHSEKALIMHH2_H3_B', 'SEKALIMHHSEKALIMHH2_H4_B', 'SEKALIMHHSEKALIMHH2_H5_B', 'SEKALIMHHSEKALIMHH2_H6_B', 'SEKALIMHHSEKALIMHH2_H7_B', 'SEKALIMHHSEKALIMHH2_H8_B', 'SEKALIMHHSEKALIMHH2_H9_B', 'SEKALIMHHSEKALIMHH2_H10_B', 'SEKALIMHHSEKALIMHH2_H11_B', 'SEKALIMHHSEKALIMHH2_H12_B', 'SEKALIMHHSEKALIMHH2_H13_B', 'SEKALIMHHSEKALIMHH2_H14_B', 'SEKALIMHHSEKALIMHH2_H15_B', 'SEKALIMHHSEKALIMHH2_H16_B', 'SEKALIMHHSEKALIMHH2_H17_B', 'SEKALIMHHSEKALIMHH2_H18_B', 'SEKALIMHHSEKALIMHH2_H19_B', 'SEKALIMHHSEKALIMHH2_H20_B', 'SEKALIMHHSEKALIMHH2_H21_B', 'SEKALIMHHSEKALIMHH2_H22_B', 'SEKALIMHHSEKALIMHH2_H23_B', 'SEKALIMHHSEKALIMHH3_H24', 'SEKALIMHHSEKALIMHH3_H25', 'SEKALIMHHSEKALIMHH3_H26', 'H24', 'H25', 'H26', 'SEKALIMHHSEKALIMHH3_H27', 'SEKALIMHHSEKALIMHH3_H28', 'SEKALIMHHSEKALIMHH3_H29', 'SEKALIMHHSEKALIMHH3_H30', 'SEKALIMHHSEKALIMHH3_H31', 'SEKALIMHHSEKALIMHH3_H32', 'SEKALIMHHSEKALIMHH3_H33', 'SEKALIMHHSEKALIMHH3_H34', 'SEKALIMHHSEKALIMHH3_H35', 'SEKALIMHHSEKALIMHH3_H36', 'SEKALIMHHSEKALIMHH3_H37', 'SEKALIMHHSEKALIMHH3_H38', 'SEKALIMHHSEKALIMHH3_H39', 'SEKALIMHHSEKALIMHH3_H40', 'SEKALIMHHSEKALIMHH3_H41', 'SEKALIMHHSEKALIMHH3_H42', 'SEKALIMHHSEKALIMHH3_H43', 'SEKALIMHHSEKALIMHH3_H44', 'SEKALIMHHSEKALIMHH3_H45', 'SEKALIMHHSEKALIMHH3_H46', 'SEKALIMHHSEKALIMHH4_H47', 'SEKALIMHHSEKALIMHH4_H48', 'SEKALIMHHSEKALIMHH4_H49', 'SEKALIMHHSEKALIMHH4_H50', 'SEKALIMHHSEKALIMHH4_H51', 'SEKALIMHHSEKALIMHH4_H52', 'SEKALIMHHSEKALIMHH4_H53', 'SEKALIMHHSEKALIMHH4_H54', 'SEKALIMHHSEKALIMHH4_H55', 'SEKALIMHHSEKALIMHH4_H56', 'SEKALIMHHSEKALIMHH4_H57', 'SEKALIMHHSEKALIMHH4_H58', 'SEKALIMHHSEKALIMHH4_H59', 'SEKALIMHHSEKALIMHH4_H60', 'SEKALIMHHSEKALIMHH4_H61', 'SEKALIMHHSEKALIMHH4_H62', 'SEKALIMHHSEKALIMHH4_H63', 'SEKALIMHHSEKALIMHH4_H64', 'SEKALIMHHSEKALIMHH4_H65', 'SEKALIMHHSEKALIMHH4_H66', 'SEKALIMHHSEKALIMHH4_H67', 'SEKALIMHHSEKALIMHH4_H68', 'SEKALIMHHSEKALIMHH4_H69', 'SEVIS_KONPOTMAN_HH_I1', 'SEVIS_KONPOTMAN_HH_I2', 'SEVIS_KONPOTMAN_HH_I3', 'SEVIS_KONPOTMAN_HH_I4', 'SEVIS_KONPOTMAN_HH_I5_1', 'SEVIS_KONPOTMAN_HH_I5_2', 'SEVIS_KONPOTMAN_HH_I6', 'SEVIS_KONPOTMAN_HH_I7', 'SEVIS_KONPOTMAN_HH_I8'], 'integer'],
            [['URI'], 'string', 'max' => 50],
            [['ID_HH_A3_FINAL', 'ID_HH_ADM_KOMIN', 'ID_HH_ADM_SEK_KOM', 'ID_HH_A5_2', 'ID_HH_A5_3', 'ID_HH_A8', 'ID_HH_A9', 'ID_HH_A10_ACC', 'ID_HH_A10_ALT', 'ID_HH_A10_LAT', 'ID_HH_A10_LNG'], 'string', 'max' => 15]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'URI' => Yii::t('app', 'Uri'),
            'ID_HH_A3_FINAL' => Yii::t('app', 'Id  Hh  A3  Final'),
            'ID_HH_ADM_KOMIN' => Yii::t('app', 'Id  Hh  Adm  Komin'),
            'ID_HH_ADM_SEK_KOM' => Yii::t('app', 'Id  Hh  Adm  Sek  Kom'),
            'ID_HH_MILIEU_RESID' => Yii::t('app', 'Id  Hh  Milieu  Resid'),
            'ID_HH_A5_1' => Yii::t('app', 'Id  Hh  A5 1'),
            'ID_HH_A5_2' => Yii::t('app', 'Id  Hh  A5 2'),
            'ID_HH_A5_3' => Yii::t('app', 'Id  Hh  A5 3'),
            'ID_HH_A6' => Yii::t('app', 'Id  Hh  A6'),
            'ID_HH_A8' => Yii::t('app', 'Id  Hh  A8'),
            'ID_HH_A8_3' => Yii::t('app', 'Id  Hh  A8 3'),
            'ID_HH_A8_4' => Yii::t('app', 'Id  Hh  A8 4'),
            'ID_HH_A8_5' => Yii::t('app', 'Id  Hh  A8 5'),
            'ID_HH_A9' => Yii::t('app', 'Id  Hh  A9'),
            'ID_HH_A10_ACC' => Yii::t('app', 'Id  Hh  A10  Acc'),
            'ID_HH_A10_ALT' => Yii::t('app', 'Id  Hh  A10  Alt'),
            'ID_HH_A10_LAT' => Yii::t('app', 'Id  Hh  A10  Lat'),
            'ID_HH_A10_LNG' => Yii::t('app', 'Id  Hh  A10  Lng'),
            'ID_HH_NO_NON_RESYDAN' => Yii::t('app', 'Id  Hh  No  Non  Resydan'),
            'ID_HH_NO_RESYDAN' => Yii::t('app', 'Id  Hh  No  Resydan'),
            'ID_HH_MANM_HH_TOTAL' => Yii::t('app', 'Id  Hh  Manm  Hh  Total'),
            'MANM_HH1_D6_1' => Yii::t('app', 'Manm  Hh1  D6 1'),
            'MANM_HH1_MALAD_QUE_CRON' => Yii::t('app', 'Manm  Hh1  Malad  Que  Cron'),
            'MANM_HH1_MALAD_NO_CRON' => Yii::t('app', 'Manm  Hh1  Malad  No  Cron'),
            'MANM_HH1_MALAD_QUE_MWA' => Yii::t('app', 'Manm  Hh1  Malad  Que  Mwa'),
            'MANM_HH1_MALAD_NO_MWA' => Yii::t('app', 'Manm  Hh1  Malad  No  Mwa'),
            'MANM_HH1_NO_MOURI' => Yii::t('app', 'Manm  Hh1  No  Mouri'),
            'BIENS_HH_F1' => Yii::t('app', 'Biens  Hh  F1'),
            'BIENS_HH_F2' => Yii::t('app', 'Biens  Hh  F2'),
            'BIENS_HH_F3_F3_1' => Yii::t('app', 'Biens  Hh  F3  F3 1'),
            'BIENS_HH_F3_F3_2' => Yii::t('app', 'Biens  Hh  F3  F3 2'),
            'BIENS_HH_F3_F3_3' => Yii::t('app', 'Biens  Hh  F3  F3 3'),
            'BIENS_HH_F3_F3_4' => Yii::t('app', 'Biens  Hh  F3  F3 4'),
            'BIENS_HH_F3_F3_5' => Yii::t('app', 'Biens  Hh  F3  F3 5'),
            'BIENS_HH_F3_F3_6' => Yii::t('app', 'Biens  Hh  F3  F3 6'),
            'BIENS_HH_F3_F3_7' => Yii::t('app', 'Biens  Hh  F3  F3 7'),
            'BIENS_HH_F3_F3_8' => Yii::t('app', 'Biens  Hh  F3  F3 8'),
            'BIENS_HH_F3_F3_9' => Yii::t('app', 'Biens  Hh  F3  F3 9'),
            'BIENS_HH_F3_F3_10' => Yii::t('app', 'Biens  Hh  F3  F3 10'),
            'BIENS_HH_F3_F3_11' => Yii::t('app', 'Biens  Hh  F3  F3 11'),
            'BIENS_HH_F3_F3_12' => Yii::t('app', 'Biens  Hh  F3  F3 12'),
            'BIENS_HH_F3_F3_13' => Yii::t('app', 'Biens  Hh  F3  F3 13'),
            'BIENS_HH_F3_F3_14' => Yii::t('app', 'Biens  Hh  F3  F3 14'),
            'BIENS_HH_F3_F3_15' => Yii::t('app', 'Biens  Hh  F3  F3 15'),
            'BIENS_HH_F3_F3_16' => Yii::t('app', 'Biens  Hh  F3  F3 16'),
            'BIENS_HH_F3_F3_17' => Yii::t('app', 'Biens  Hh  F3  F3 17'),
            'BIENS_HH_F3_F3_18' => Yii::t('app', 'Biens  Hh  F3  F3 18'),
            'BIENS_HH_F4' => Yii::t('app', 'Biens  Hh  F4'),
            'BIENS_HH_F5_F5_1' => Yii::t('app', 'Biens  Hh  F5  F5 1'),
            'BIENS_HH_F5_F5_2' => Yii::t('app', 'Biens  Hh  F5  F5 2'),
            'BIENS_HH_F5_F5_3' => Yii::t('app', 'Biens  Hh  F5  F5 3'),
            'BIENS_HH_F5_F5_4' => Yii::t('app', 'Biens  Hh  F5  F5 4'),
            'BIENS_HH_F5_F5_5' => Yii::t('app', 'Biens  Hh  F5  F5 5'),
            'BIENS_HH_F5_F5_6' => Yii::t('app', 'Biens  Hh  F5  F5 6'),
            'BIENS_HH_F5_F5_7' => Yii::t('app', 'Biens  Hh  F5  F5 7'),
            'BIENS_HH_F6' => Yii::t('app', 'Biens  Hh  F6'),
            'BIENS_HH_F7' => Yii::t('app', 'Biens  Hh  F7'),
            'BIENS_HH_F8_1' => Yii::t('app', 'Biens  Hh  F8 1'),
            'BIENS_HH_F8_2' => Yii::t('app', 'Biens  Hh  F8 2'),
            'BIENS_HH_F9' => Yii::t('app', 'Biens  Hh  F9'),
            'BIENS_HH_F9_1' => Yii::t('app', 'Biens  Hh  F9 1'),
            'BIENS_HH_F10' => Yii::t('app', 'Biens  Hh  F10'),
            'BIENS_HH_F11' => Yii::t('app', 'Biens  Hh  F11'),
            'BIENS_HH_F12' => Yii::t('app', 'Biens  Hh  F12'),
            'BIENS_HH_F12_0' => Yii::t('app', 'Biens  Hh  F12 0'),
            'BIENS_HH_F13' => Yii::t('app', 'Biens  Hh  F13'),
            'BIENS_HH_F14' => Yii::t('app', 'Biens  Hh  F14'),
            'BIENS_HH_F15' => Yii::t('app', 'Biens  Hh  F15'),
            'BIENS_HH_F15_0' => Yii::t('app', 'Biens  Hh  F15 0'),
            'BIENS_HH_F16' => Yii::t('app', 'Biens  Hh  F16'),
            'BIENS_HH_F17' => Yii::t('app', 'Biens  Hh  F17'),
            'BIENS_HH_F18' => Yii::t('app', 'Biens  Hh  F18'),
            'BIENS_HH_F19' => Yii::t('app', 'Biens  Hh  F19'),
            'BIENS_HH_F20' => Yii::t('app', 'Biens  Hh  F20'),
            'BIENS_HH_G1' => Yii::t('app', 'Biens  Hh  G1'),
            'BIENS_HH_G2' => Yii::t('app', 'Biens  Hh  G2'),
            'SEKALIMHHSEKALIMHH1_H1' => Yii::t('app', 'Sekalimhhsekalimhh1  H1'),
            'SEKALIMHHSEKALIMHH1_H2' => Yii::t('app', 'Sekalimhhsekalimhh1  H2'),
            'SEKALIMHHSEKALIMHH1_H3' => Yii::t('app', 'Sekalimhhsekalimhh1  H3'),
            'SEKALIMHHSEKALIMHH1_H4' => Yii::t('app', 'Sekalimhhsekalimhh1  H4'),
            'SEKALIMHHSEKALIMHH1_H5' => Yii::t('app', 'Sekalimhhsekalimhh1  H5'),
            'SEKALIMHHSEKALIMHH1_H6' => Yii::t('app', 'Sekalimhhsekalimhh1  H6'),
            'SEKALIMHHSEKALIMHH1_H7' => Yii::t('app', 'Sekalimhhsekalimhh1  H7'),
            'SEKALIMHHSEKALIMHH1_H8' => Yii::t('app', 'Sekalimhhsekalimhh1  H8'),
            'SEKALIMHHSEKALIMHH1_H9' => Yii::t('app', 'Sekalimhhsekalimhh1  H9'),
            'SEKALIMHHSEKALIMHH1_H10' => Yii::t('app', 'Sekalimhhsekalimhh1  H10'),
            'SEKALIMHHSEKALIMHH1_H11' => Yii::t('app', 'Sekalimhhsekalimhh1  H11'),
            'SEKALIMHHSEKALIMHH1_H12' => Yii::t('app', 'Sekalimhhsekalimhh1  H12'),
            'SEKALIMHHSEKALIMHH1_H13' => Yii::t('app', 'Sekalimhhsekalimhh1  H13'),
            'SEKALIMHHSEKALIMHH1_H14' => Yii::t('app', 'Sekalimhhsekalimhh1  H14'),
            'SEKALIMHHSEKALIMHH1_H15' => Yii::t('app', 'Sekalimhhsekalimhh1  H15'),
            'SEKALIMHHSEKALIMHH1_H16' => Yii::t('app', 'Sekalimhhsekalimhh1  H16'),
            'SEKALIMHHSEKALIMHH1_H17' => Yii::t('app', 'Sekalimhhsekalimhh1  H17'),
            'SEKALIMHHSEKALIMHH1_H18' => Yii::t('app', 'Sekalimhhsekalimhh1  H18'),
            'SEKALIMHHSEKALIMHH1_H19' => Yii::t('app', 'Sekalimhhsekalimhh1  H19'),
            'SEKALIMHHSEKALIMHH1_H20' => Yii::t('app', 'Sekalimhhsekalimhh1  H20'),
            'SEKALIMHHSEKALIMHH1_H21' => Yii::t('app', 'Sekalimhhsekalimhh1  H21'),
            'SEKALIMHHSEKALIMHH1_H22' => Yii::t('app', 'Sekalimhhsekalimhh1  H22'),
            'SEKALIMHHSEKALIMHH1_H23' => Yii::t('app', 'Sekalimhhsekalimhh1  H23'),
            'SEKALIMHHSEKALIMHH2_H1_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H1  B'),
            'SEKALIMHHSEKALIMHH2_H2_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H2  B'),
            'SEKALIMHHSEKALIMHH2_H3_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H3  B'),
            'SEKALIMHHSEKALIMHH2_H4_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H4  B'),
            'SEKALIMHHSEKALIMHH2_H5_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H5  B'),
            'SEKALIMHHSEKALIMHH2_H6_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H6  B'),
            'SEKALIMHHSEKALIMHH2_H7_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H7  B'),
            'SEKALIMHHSEKALIMHH2_H8_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H8  B'),
            'SEKALIMHHSEKALIMHH2_H9_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H9  B'),
            'SEKALIMHHSEKALIMHH2_H10_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H10  B'),
            'SEKALIMHHSEKALIMHH2_H11_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H11  B'),
            'SEKALIMHHSEKALIMHH2_H12_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H12  B'),
            'SEKALIMHHSEKALIMHH2_H13_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H13  B'),
            'SEKALIMHHSEKALIMHH2_H14_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H14  B'),
            'SEKALIMHHSEKALIMHH2_H15_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H15  B'),
            'SEKALIMHHSEKALIMHH2_H16_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H16  B'),
            'SEKALIMHHSEKALIMHH2_H17_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H17  B'),
            'SEKALIMHHSEKALIMHH2_H18_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H18  B'),
            'SEKALIMHHSEKALIMHH2_H19_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H19  B'),
            'SEKALIMHHSEKALIMHH2_H20_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H20  B'),
            'SEKALIMHHSEKALIMHH2_H21_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H21  B'),
            'SEKALIMHHSEKALIMHH2_H22_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H22  B'),
            'SEKALIMHHSEKALIMHH2_H23_B' => Yii::t('app', 'Sekalimhhsekalimhh2  H23  B'),
            'SEKALIMHHSEKALIMHH3_H24' => Yii::t('app', 'Sekalimhhsekalimhh3  H24'),
            'SEKALIMHHSEKALIMHH3_H25' => Yii::t('app', 'Sekalimhhsekalimhh3  H25'),
            'SEKALIMHHSEKALIMHH3_H26' => Yii::t('app', 'Sekalimhhsekalimhh3  H26'),
            'H24' => Yii::t('app', 'H24'),
            'H25' => Yii::t('app', 'H25'),
            'H26' => Yii::t('app', 'H26'),
            'SEKALIMHHSEKALIMHH3_H27' => Yii::t('app', 'Sekalimhhsekalimhh3  H27'),
            'SEKALIMHHSEKALIMHH3_H28' => Yii::t('app', 'Sekalimhhsekalimhh3  H28'),
            'SEKALIMHHSEKALIMHH3_H29' => Yii::t('app', 'Sekalimhhsekalimhh3  H29'),
            'SEKALIMHHSEKALIMHH3_H30' => Yii::t('app', 'Sekalimhhsekalimhh3  H30'),
            'SEKALIMHHSEKALIMHH3_H31' => Yii::t('app', 'Sekalimhhsekalimhh3  H31'),
            'SEKALIMHHSEKALIMHH3_H32' => Yii::t('app', 'Sekalimhhsekalimhh3  H32'),
            'SEKALIMHHSEKALIMHH3_H33' => Yii::t('app', 'Sekalimhhsekalimhh3  H33'),
            'SEKALIMHHSEKALIMHH3_H34' => Yii::t('app', 'Sekalimhhsekalimhh3  H34'),
            'SEKALIMHHSEKALIMHH3_H35' => Yii::t('app', 'Sekalimhhsekalimhh3  H35'),
            'SEKALIMHHSEKALIMHH3_H36' => Yii::t('app', 'Sekalimhhsekalimhh3  H36'),
            'SEKALIMHHSEKALIMHH3_H37' => Yii::t('app', 'Sekalimhhsekalimhh3  H37'),
            'SEKALIMHHSEKALIMHH3_H38' => Yii::t('app', 'Sekalimhhsekalimhh3  H38'),
            'SEKALIMHHSEKALIMHH3_H39' => Yii::t('app', 'Sekalimhhsekalimhh3  H39'),
            'SEKALIMHHSEKALIMHH3_H40' => Yii::t('app', 'Sekalimhhsekalimhh3  H40'),
            'SEKALIMHHSEKALIMHH3_H41' => Yii::t('app', 'Sekalimhhsekalimhh3  H41'),
            'SEKALIMHHSEKALIMHH3_H42' => Yii::t('app', 'Sekalimhhsekalimhh3  H42'),
            'SEKALIMHHSEKALIMHH3_H43' => Yii::t('app', 'Sekalimhhsekalimhh3  H43'),
            'SEKALIMHHSEKALIMHH3_H44' => Yii::t('app', 'Sekalimhhsekalimhh3  H44'),
            'SEKALIMHHSEKALIMHH3_H45' => Yii::t('app', 'Sekalimhhsekalimhh3  H45'),
            'SEKALIMHHSEKALIMHH3_H46' => Yii::t('app', 'Sekalimhhsekalimhh3  H46'),
            'SEKALIMHHSEKALIMHH4_H47' => Yii::t('app', 'Sekalimhhsekalimhh4  H47'),
            'SEKALIMHHSEKALIMHH4_H48' => Yii::t('app', 'Sekalimhhsekalimhh4  H48'),
            'SEKALIMHHSEKALIMHH4_H49' => Yii::t('app', 'Sekalimhhsekalimhh4  H49'),
            'SEKALIMHHSEKALIMHH4_H50' => Yii::t('app', 'Sekalimhhsekalimhh4  H50'),
            'SEKALIMHHSEKALIMHH4_H51' => Yii::t('app', 'Sekalimhhsekalimhh4  H51'),
            'SEKALIMHHSEKALIMHH4_H52' => Yii::t('app', 'Sekalimhhsekalimhh4  H52'),
            'SEKALIMHHSEKALIMHH4_H53' => Yii::t('app', 'Sekalimhhsekalimhh4  H53'),
            'SEKALIMHHSEKALIMHH4_H54' => Yii::t('app', 'Sekalimhhsekalimhh4  H54'),
            'SEKALIMHHSEKALIMHH4_H55' => Yii::t('app', 'Sekalimhhsekalimhh4  H55'),
            'SEKALIMHHSEKALIMHH4_H56' => Yii::t('app', 'Sekalimhhsekalimhh4  H56'),
            'SEKALIMHHSEKALIMHH4_H57' => Yii::t('app', 'Sekalimhhsekalimhh4  H57'),
            'SEKALIMHHSEKALIMHH4_H58' => Yii::t('app', 'Sekalimhhsekalimhh4  H58'),
            'SEKALIMHHSEKALIMHH4_H59' => Yii::t('app', 'Sekalimhhsekalimhh4  H59'),
            'SEKALIMHHSEKALIMHH4_H60' => Yii::t('app', 'Sekalimhhsekalimhh4  H60'),
            'SEKALIMHHSEKALIMHH4_H61' => Yii::t('app', 'Sekalimhhsekalimhh4  H61'),
            'SEKALIMHHSEKALIMHH4_H62' => Yii::t('app', 'Sekalimhhsekalimhh4  H62'),
            'SEKALIMHHSEKALIMHH4_H63' => Yii::t('app', 'Sekalimhhsekalimhh4  H63'),
            'SEKALIMHHSEKALIMHH4_H64' => Yii::t('app', 'Sekalimhhsekalimhh4  H64'),
            'SEKALIMHHSEKALIMHH4_H65' => Yii::t('app', 'Sekalimhhsekalimhh4  H65'),
            'SEKALIMHHSEKALIMHH4_H66' => Yii::t('app', 'Sekalimhhsekalimhh4  H66'),
            'SEKALIMHHSEKALIMHH4_H67' => Yii::t('app', 'Sekalimhhsekalimhh4  H67'),
            'SEKALIMHHSEKALIMHH4_H68' => Yii::t('app', 'Sekalimhhsekalimhh4  H68'),
            'SEKALIMHHSEKALIMHH4_H69' => Yii::t('app', 'Sekalimhhsekalimhh4  H69'),
            'SEVIS_KONPOTMAN_HH_I1' => Yii::t('app', 'Sevis  Konpotman  Hh  I1'),
            'SEVIS_KONPOTMAN_HH_I2' => Yii::t('app', 'Sevis  Konpotman  Hh  I2'),
            'SEVIS_KONPOTMAN_HH_I3' => Yii::t('app', 'Sevis  Konpotman  Hh  I3'),
            'SEVIS_KONPOTMAN_HH_I4' => Yii::t('app', 'Sevis  Konpotman  Hh  I4'),
            'SEVIS_KONPOTMAN_HH_I5_1' => Yii::t('app', 'Sevis  Konpotman  Hh  I5 1'),
            'SEVIS_KONPOTMAN_HH_I5_2' => Yii::t('app', 'Sevis  Konpotman  Hh  I5 2'),
            'SEVIS_KONPOTMAN_HH_I6' => Yii::t('app', 'Sevis  Konpotman  Hh  I6'),
            'SEVIS_KONPOTMAN_HH_I7' => Yii::t('app', 'Sevis  Konpotman  Hh  I7'),
            'SEVIS_KONPOTMAN_HH_I8' => Yii::t('app', 'Sevis  Konpotman  Hh  I8'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChroniques() {
        return $this->hasMany(Chroniques::className(), ['URI' => 'URI']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembres() {
        return $this->hasMany(Membre::className(), ['URI' => 'URI']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecents() {
        return $this->hasMany(Recents::className(), ['URI' => 'URI']);
    }

    public function getMembreTable($uri) {
        $member = Membre::find()->where(['URI' => $uri])->all();
        return $member;
    }

    public function getChroniqueTable($uri) {
        $member = Chroniques::find()->where(['URI' => $uri])->all();
        return $member;
    }
    
    public function getFamilleTable($uri) {
        $family = Famille::find()->where(['URI' => $uri])->all();
        return $family;
    }

    public function algoForUrbanArea($uri) {
        $member_famille = $this->getMembreTable($uri);
        $member_chronique = $this->getChroniqueTable($uri);
        $famille_ = $this->getFamilleTable($uri);
        
        // Count the familly size 
        $family_size = 0;
        foreach ($member_famille as $mf) {
            $family_size++;
        }
        /**
         * Calcul de l'indicateur #1 
         * Household Demographic Composition
         */
        $count_child_under_15 = 0; // count child under 15
        $count_eldery = 0; // count eldery person >= 65 
        $is_couple = False; // Is en couple b7 == 2

        $d_1_1 = 0; // d_ variable for indicator 1
        $cat_fam; // Categorie de famille 


        foreach ($member_famille as $mf) {

            if ($mf['b3'] <= 15) { // Compte les membres de 15 ans et moins
                $count_child_under_15++;
            }

            if ($mf['b2'] >= 65) { // Compte les personnes ages de 65 ans et plus
                $count_eldery++;
            }

            if ($mf['b7'] == 2) { // Verifie qu'il a une relation de couple dans la famille
                $is_couple = True;
            }
        }

        /**
         * Classifie les familles en categorie suivant le tableau 2 du document
         */
        if ($count_child_under_15 == 0) {
            $cat_fam = 1;
        }

        if ($count_child_under_15 > 0 && $is_couple == False) {
            $cat_fam = 2;
        }

        if ($count_child_under_15 > 0 && $is_couple == True) {
            $cat_fam = 3;
        }

        if ($count_child_under_15 > 0 && $is_couple == False && $count_eldery > 0) {
            $cat_fam = 4;
        }

        if ($count_child_under_15 > 0 && $is_couple == True && $count_eldery > 0) {
            $cat_fam = 5;
        }
        /**
         * Affecte une valeur a la variable $d_1_1 suivant la categorie de la famille
         */
        switch ($cat_fam) {
            case 1:
                $d_1_1 = 0;
                break;
            case 2:
                $d_1_1 = 0.726013;
            case 3:
                $d_1_1 = 0.478189;
                break;
            case 4:
                $d_1_1 = 0.157214;
                break;
            case 5:
                $d_1_1 = 3.000000;
                break;
        }

        $prisk_1_1 = 3; // prisk_1_1 est egal a 1 page 7 du document
        $hdr_1_1 = ($d_1_1) / pow($prisk_1_1, 0.5); // Calcul de household deprivation ratio (hdr) 

        /**
         * Indicateur #2 
         * Children under 5 years old 
         */
        $count_child_0_4 = 0;
        $count_child_18_64 = 0;
        $d_1_2 = 0;

        foreach ($member_famille as $mf) {
            if ($mf['b3'] <= 4) {
                $count_child_0_4++;
            }

            if ($mf['b3'] >= 18 && $mf['b3'] <= 64) {
                $count_child_18_64++;
            }
        }

        $d_1_2 = $count_child_0_4;
        $prisk_1_2 = $count_child_18_64;
        if ($prisk_1_2 != 0) {
            $hdr_1_2 = $d_1_2 / pow($prisk_1_2, 0.5);
        } else {
            $hdr_1_2 = 0;
        }

        /**
         *  HEALTH 
         * Indicateur # 3
         * Chronically ILL 
         * 
         */
        $d_2_1 = 0;
        $count_chronically_ill = 0;

        foreach ($member_chronique as $mc) {
            if ($mc['MANM_HH1_HH_MALAD_81_E1_1_81'] == 1) {
                $count_chronically_ill++;
            }
        }

        if ($count_chronically_ill > $family_size) {
            $d_2_1 = $family_size;
        } else {
            $d_2_1 = $count_chronically_ill;
        }
        $prisk_2_1 = $family_size;
        if ($prisk_2_1 != 0) {
            $hdr_2_1 = $d_2_1 / pow($prisk_2_1, 0.5);
        } else {
            $hdr_2_1 = 0;
        }

        /**
         * Indicateur # 4
         * Disabled or permanently injured but not chronically ill
         * 
         */
        $d_2_2 = 0;
        $count_disabled = 0;

        foreach ($member_chronique as $mc) {
            if ($mc['MANM_HH1_HH_MALAD_81_E1_1_81'] == 2) {
                if ($mc['MANM_HH1_HH_MALAD_81_E3_1_81'] == 1) {
                    $count_disabled++;
                }
            }
        }

        if ($count_disabled > $family_size) {
            $d_2_2 = $family_size;
        } else {
            $d_2_2 = $count_disabled;
        }

        $prisk_2_2 = $family_size;
        if ($prisk_2_2 != 0) {
            $hdr_2_2 = $d_2_2 / pow($prisk_2_2, 0.5);
        } else {
            $hdr_2_2 = 0;
        }

        /**
         * EDUCATION 
         * Indicateur #5 
         * ILLITERACY 
         */
        $count_illiterate = 0;
        $d_3_1 = 0;
        $prisk_3_1 = 0;

        foreach ($member_famille as $mf) {
            if ($mf['b3'] >= 15) {
                if ($mf['c1'] == 2) {
                    $count_illiterate++;
                }
                $prisk_3_1++;
            }
        }

        $d_3_1 = $count_illiterate;

        if ($prisk_3_1 != 0) {
            $hdr_3_1 = $d_3_1 / pow($prisk_3_1, 0.5);
        } else {
            $hdr_3_1 = 0;
        }

        /**
         * Indicateur #6
         * Lack of basic education 
         * 
         */
        $count_not_complete_basic_edu = 0;
        $d_3_2 = 0;
        $count_member_21_plus = 0;

        foreach ($member_famille as $mf) {
            if ($mf['c1'] == 1) {
                if ($mf['b3'] >= 21 && $mf['c1_5'] <= 8) {
                    $count_not_complete_basic_edu++;
                }
            }
            if ($mf['b3'] >= 21) {
                $count_member_21_plus++;
            }
        }

        $d_3_2 = $count_not_complete_basic_edu;
        $prisk_3_2 = $count_member_21_plus;

        if ($prisk_3_2 != 0) {
            $hdr_3_2 = $d_3_2 / pow($prisk_3_2, 0.5);
        } else {
            $hdr_3_2 = 0;
        }

        /**
         * 
         * Indicateur #7 
         * School non-attendance 
         * 
         */
        $count_member_not_at_school = 0;
        $d_3_3 = 0;
        $count_member_schooling_age = 0;
        foreach ($member_famille as $mf) {
            if ($mf['b3'] >= 3 && $mf['b3'] <= 18) {
                $count_member_schooling_age++;
                if ($mf['c1_3'] == 2) {
                    $count_member_not_at_school++;
                }
            }
        }

        $d_3_3 = $count_member_not_at_school;
        $prisk_3_3 = $count_member_schooling_age;

        if ($prisk_3_3 != 0) {
            $hdr_3_3 = $d_3_3 / pow($prisk_3_3, 0.5);
        } else {
            $hdr_3_3 = 0;
        }

        /**
         * Indicateur 8 
         * School LAG
         * 
         */
        $d_3_4 = 0;
        $expect_year_e;
        $gap = [];
        $i = 0;
        $count_member_3_20 = 0;
        foreach ($member_famille as $mf) {
            if ($mf['b3'] >= 3 && $mf['b3'] <= 20) {
                $count_member_3_20++;

                if ($mf['b3'] < 7) {
                    $expect_year_e = 0;
                } else {
                    switch ($mf['b3']) {
                        case 7:
                            $expect_year_e = 1;
                            break;
                        case 8:
                            $expect_year_e = 2;
                            break;
                        case 9:
                            $expect_year_e = 3;
                            break;
                        case 10:
                            $expect_year_e = 4;
                            break;
                        case 11:
                            $expect_year_e = 5;
                            break;
                        case 12:
                            $expect_year_e = 6;
                            break;
                        case 13:
                            $expect_year_e = 7;
                            break;
                        case 14:
                            $expect_year_e = 8;
                            break;
                        case 15:
                            $expect_year_e = 9;
                            break;
                        case 16:
                            $expect_year_e = 10;
                            break;
                        case 17:
                            $expect_year_e = 11;
                            break;
                        case 18:
                            $expect_year_e = 12;
                            break;
                    }
                    if ($mf['b3'] > 18) {
                        $expect_year_e = 13;
                    }
                }

                // GAP calculation 
                if ($expect_year_e == 0) {
                    $gap[$i] = 0;
                    $i++;
                } else {
                    $var_gap = $expect_year_e - $mf['c1_5'];
                    if ($var_gap < 0) {
                        $gap[$i] = 0;
                        $i++;
                    } else {
                        $gap[$i] = $var_gap;
                        $i++;
                    }
                }
            }
        }

        // Analyse the gap
        $result_gap = [];
        $j = 0;
        for ($x = 0; $x < count($gap); $x++) {
            if ($gap[$x] == 0) {
                $result_gap[$j] = 0;
                $j++;
            }

            if ($gap[$x] > 0 && $gap[$x] <= 3) {
                $result_gap[$j] = 1;
                $j++;
            }

            if ($gap[$x] >= 4) {
                $result_gap[$j] = 2;
                $j++;
            }
        }

        $total_count = array_sum($result_gap);

        if ($total_count > $count_member_3_20) {
            $d_3_4 = $count_member_3_20;
        } else {
            $d_3_4 = $total_count;
        }

        $prisk_3_4 = $count_member_3_20;

        if ($prisk_3_4 != 0) {
            $hdr_3_4 = $d_3_4 / pow($prisk_3_4, 0.5);
        } else {
            $hdr_3_4 = 0;
        }


        /**
         * LABOUR Condition 
         * Inactivity
         * Indicateur #9 
         * 
         */
        $d_4_1 = 0;
        $count_member_inactive = 0;
        $count_member_active = 0;
        $count_member_active_inactive = 0;
        $active_array = [1, 2, 3, 4, 5, 6, 8, 9];
        $inactive_array = [7, 10, 11, 12];
        foreach ($member_famille as $mf) {
            if ($mf['b3'] >= 18 && $mf['b3'] <= 64) {
                $count_member_active_inactive++;
                if (in_array($mf['d5'], $active_array)) {
                    $count_member_active++;
                }
                if (in_array($mf['d5'], $inactive_array)) {
                    $count_member_inactive++;
                }
            }
        }

        $d_4_1 = $count_member_inactive;
        $prisk_4_1 = $count_member_active_inactive;
        if ($prisk_4_1 != 0) {
            $hdr_4_1 = $d_4_1 / pow($prisk_4_1, 0.5);
        } else {
            $hdr_4_1 = 0;
        }

        /**
         * Unemployment 
         * Indicateur #10
         *
         */
        $count_member_unemployed = 0;
        $d_4_2 = 0;

        foreach ($member_famille as $mf) {
            if (in_array($mf['d5'], $active_array) && ($mf['b3'] >= 18 && $mf['b3'] <= 64)) {
                if ($mf['d2'] == 2) {
                    $count_member_unemployed++;
                }
            }
        }
        $d_4_2 = $count_member_unemployed;
        $prisk_4_2 = $count_member_active;

        if ($prisk_4_2 != 0) {
            $hdr_4_2 = $d_4_2 / pow($prisk_4_2, 0.5);
        } else {
            $hdr_4_2 = 0;
        }

        /**
         * Child Labor 
         * Indicateur #11 
         * 
         */
        $count_child_labourer_1 = 0;
        $count_child_labourer_2 = 0;
        $d_4_3 = 0; 
        
        foreach ($member_famille as $mf) {
            if(in_array($mf['d5'],$active_array) && ($mf['b3'] >= 10 && $mf['b3'] <= 12) ) {
                $count_child_labourer_1++;
            }
            if(in_array($mf['d5'],$active_array) && ($mf['b3'] >= 13 && $mf['b3'] <= 15) ) {
                $count_child_labourer_2++;
            }
        }
        
        $d_4_3 = $count_child_labourer_1*1.5 + $count_child_labourer_2; 
        $prisk_4_3 = $count_child_labourer_1*1.5 + $count_child_labourer_2;

        if($prisk_4_3 != 0){
            $hdr_4_3 = $d_4_3/pow($prisk_4_3,0.5);
        }
        else{
            $hdr_4_3 = 0;
        }
        
        /**
         * FOOD SECURITY 
         * Absence of food 
         * Indicator #12
         * 
         */
        
        $d_5_1 = 0;
        $prisk_5_1 = 10;
        foreach($famille_ as $f){
            switch($f['H24']){
                case 0:
                    $d_5_1 = 0;
                    break;
                case 1:
                    $d_5_1 = 3;
                    break;
                case 2:
                    $d_5_1 = 10;
                    break;
            }
        }
        
        $hdr_5_1 = $d_5_1/pow($prisk_5_1,0.5);
        
        /*
         * Hunger 
         * Indicator #13 
         */
        
        $d_5_2 = 0;
        $prisk_5_2 = 10;
        
        foreach($famille_ as $f){
            
            switch($f['H25']){
                case 0:
                    $d_5_2 = 0;
                    break;
                case 1:
                    $d_5_2 = 3;
                    break;
                case 2:
                    $d_5_2 = 10;
                    break;
            }
        }
        
         $hdr_5_2 = $d_5_2/pow($prisk_5_2,0.5);
         
         /**
          * Restricted Consumption
          * Indicator #14 
          */
        
        $d_5_3 = 0;
        $prisk_5_3 = 10;
        
        foreach($famille_ as $f){
            
            switch($f['H26']){
                case 0:
                    $d_5_3 = 0;
                    break;
                case 1:
                    $d_5_3 = 3;
                    break;
                case 2:
                    $d_5_3 = 10;
                    break;
            }
        }
        
        $hdr_5_3 = $d_5_3/pow($prisk_5_3,0.5);
        
        /**
         * Ressource at Home 
         * Absence of remittances or Benefits 
         * Indicator #15 
         */
        $d_6_1 = 0;
        $prisk_6_1 = 0;
        $hdr_6_1 = 0; // This variable is not considering in the final calculation due to lack of information in the "Formulaire d'enquete"
        
        
        /**
         * Dwelling conditions 
         * INidcator #16 
         * 
         */
        
        $d_6_2 = 0;
        $prisk_6_2 = 3; 
        $cat_floor_1 = [1,2,6];
        $cat_roof_1 = [1,2,3,4];
        $cat_wall_1 = [1,2,3,4,7,8];
        $floor = 0;
        $wall = 0;
        $roof = 0;
        foreach($famille_ as $f){
            // Determine the floor material
            if(in_array($f['BIENS_HH_F9'], $cat_floor_1)){
                $floor = 1;
            }
            else {
                $floor = 0;
            }
            
            if(in_array($f['BIENS_HH_F8_2'],$cat_wall_1)){
                $wall = 1;
            }
            else{
                $wall = 0;
            }
            
            if(in_array($f['BIENS_HH_F8_1'],$cat_roof_1)){
                $roof = 1;
            }
            else{
                $roof = 0;
            }
            
        }
        $d_6_2 = ($floor*0.6623217 + $wall*0.2591846 + $roof*0.0785398)*3;
        $hdr_6_2 = $d_6_2/pow($prisk_6_2,0.5); 
        
        /**
         * 
         * Overcrowding  
         * Indicator : 17
         */
        
        $number_of_romm = 0;
        $ratio_room_member = 0;
        $d_6_3 = 0;
        $prisk_6_3 = 10;
        foreach($famille_ as $f){
            $number_of_romm = $f['BIENS_HH_F6'];
        }
        
        if($number_of_romm != 0 && $number_of_romm != null && is_int($number_of_romm) ){
            $ratio_room_member = $family_size/$number_of_romm; 
        }
        
        if($ratio_room_member > 10){
            $d_6_3 = 10;
        }
        elseif($ratio_room_member < 4.5){
            $d_6_3 = 0;
        }else{
            $d_6_3 = $ratio_room_member;
        }
        
        $hdr_6_3 = $d_6_3/pow($prisk_6_3,0.5);
        
        /**
         * Deprived Lighting Access 
         * Indicator #18 
         * 
         */
        
        $d_7_1 = 0; 
        $prisk_7_1 = 2; 
        $dum_1_7_1 = 0;
        $dum_2_7_1 = 0;
        $limye = [1,2,3];
        $dife = [1,2];
        foreach($famille_ as $f){
            if(in_array($f['BIENS_HH_F9_1'],$limye)){
                $dum_1_7_1 = 1;
            }else{
                $dum_1_7_1 = 0;
            }
            
            if(in_array($f['BIENS_HH_F11'],$dife)){
                $dum_2_7_1 = 1;
            }else{
                $dum_2_7_1 = 0;
            }
            
        }
        
        $d_7_1 = $dum_1_7_1*0.26328043 + $dum_2_7_1*0.73671957;
        
        $hdr_7_1 = $d_7_1/pow($prisk_7_1,0.5);
        
        
        /**
         * Deprived Access to water 
         * Indicator #19 
         */
       
        $d_7_2 = 0;
        $prisk_7_2 = 2; 
        $dum_1_7_2 = 0;
        $dum_2_7_2 = 0;
        //$dlo_potab = [];
        $dlo_sevi = [1,2,4,5];
        
        foreach($famille_ as $f){
            // For potable water in urban area 
         if($f['BIENS_HH_F15_0']!=8){
             $dum_1_7_2 = 1;
         }else{
             $dum_1_7_2 = 0;
         }
         // For cleaning water in urban area
         if(in_array($f['BIENS_HH_F15'], $dlo_sevi)){
             $dum_2_7_2 = 1;
         }else{
             $dum_2_7_2 = 0;
         }
         
        }
        
        $d_7_2 = $dum_1_7_2*0.58323448 + $dum_2_7_2*0.41676552;
        $hdr_7_2 = $d_7_2/pow($prisk_7_2,0.5);
        
        /**
         * Deprived sanitation conditions 
         * Indicator # 20 
         */
        
        $d_7_3 = 0;
        $prisk_7_3 = 2;
        $dum_1_7_3 = 0;
        $dum_2_7_3 = 0;
        $twalet = [1,2];
        $fatra = [2,3,6,7];
        
        foreach($famille_ as $f){
           if(in_array($f['BIENS_HH_F10'], $twalet)){
               $dum_1_7_3 = 1;
           } else{
               $dum_1_7_3;
           }
           
           if(in_array($f['SEVIS_KONPOTMAN_HH_I5_2'], $fatra)){
               $dum_2_7_3 = 1;
           }else{
               $dum_2_7_3 = 0;
           }
        }
        
        $d_7_3 = $dum_1_7_3*0.97436228 + $dum_2_7_3*0.02563772;
        
        $hdr_7_3 = $d_7_3/pow($prisk_7_3,0.5);
        
       
        
        /**** Make calculation from step 2 to 8 *
         *  During this step the variable will prefixe as stepi_j_k
         *  where i = the value of the step 
         * j = the category of indicator 
         * k = the number of the indicator
         */
        
        
        /*
         * Step 3 : Elevate all $hdr_i_j to the power of beta 
         */
        $beta = 0.8;
        $step_3_1_1 = pow($hdr_1_1,$beta); //1
        $step_3_1_2 = pow($hdr_1_2,$beta); //2
        
        $step_3_2_1 = pow($hdr_2_1,$beta); //3
        $step_3_2_2 = pow($hdr_2_2,$beta); //4
        
        $step_3_3_1 = pow($hdr_3_1,$beta); //5
        $step_3_3_2 = pow($hdr_3_2,$beta); //6
        $step_3_3_3 = pow($hdr_3_3,$beta); //7
        $step_3_3_4 = pow($hdr_3_4,$beta); //8
        
        $step_3_4_1 = pow($hdr_4_1,$beta); //9
        $step_3_4_2 = pow($hdr_4_2,$beta); //10
        $step_3_4_3 = pow($hdr_4_3,$beta); //11
        
        $step_3_5_1 = pow($hdr_5_1,$beta); // 12
        $step_3_5_2 = pow($hdr_5_2,$beta); // 13
        $step_3_5_3 = pow($hdr_5_3,$beta); // 14
        
        $step_3_6_1 = pow($hdr_6_1,$beta); // 15
        $step_3_6_2 = pow($hdr_6_2,$beta); // 16
        $step_3_6_3 = pow($hdr_6_3,$beta); // 17
        
        $step_3_7_1 = pow($hdr_7_1,$beta); // 18
        $step_3_7_2 = pow($hdr_7_2,$beta); // 19
        $step_3_7_3 = pow($hdr_7_3,$beta); // 20
        
        /**** End of Step 3 *****/
        
        /*
         * Start of step 4 
         * Multiply by the weight in table A3 
         * 
         */
        $step_4_1_1 = $step_3_1_1*0.0429314994696670; 
        $step_4_1_2 = $step_3_1_2*0.0902619457321613;
        
        $step_4_2_1 = $step_3_2_1*0.0492349223596979;
        $step_4_2_2 = $step_3_2_2*0.0245334959872276;
        
        $step_4_3_1 = $step_3_3_1*0.0405288909750612;
        $step_4_3_2 = $step_3_3_2*0.0560308397563253;
        $step_4_3_3 = $step_3_3_3*0.0509274346660841;
        $step_4_3_4 = $step_3_3_4*0.0617162252859970;
        
        $step_4_4_1 = $step_3_4_1*0.0402014273313146;
        $step_4_4_2 = $step_3_4_2*0.0528897275638515;
        $step_4_4_3 = $step_3_4_3*0.0410536216067491;
        
        $step_4_5_1 = $step_3_5_1*0.0494011139694204;
        $step_4_5_2 = $step_3_5_2*0.0245334959872276;
        $step_4_5_3 = $step_3_5_3*0.0259645459513483;
        
        $step_4_6_1 = $step_3_6_1*0.0438113000009370;
        $step_4_6_2 = $step_3_6_2*0.0633357834303214;
        $step_4_6_3 = $step_3_6_3*0.0479693955686851;
        
        $step_4_7_1 = $step_3_7_1*0.0769524308370264;
        $step_4_7_2 = $step_3_7_2*0.0777533690769001;
        $step_4_7_3 = $step_3_7_3*0.0630765423973395;
        
        /************* END OF STEP 4  ****************/
        
        /**
         * Start step 5 
         * Sum the 20 indicator obtain in step 4 
         */
        
        $sigmaStep_5 = $step_4_1_1 + $step_4_1_2 + $step_4_2_1 + $step_4_2_2 + $step_4_3_1 + $step_4_3_2 + $step_4_3_3 + $step_4_3_4 + $step_4_4_1 + $step_4_4_2 + $step_4_4_3 + $step_4_5_1 + $step_4_5_2 + $step_4_5_3 + $step_4_6_1 + $step_4_6_2 + $step_4_6_3 + $step_4_7_1 + $step_4_7_2 + $step_4_7_3; 
        $step_5 = pow($sigmaStep_5,1/$beta); 
        
        /********** END OF STEP 5 ********************/
        
        /**
         * Start step 6
         * 
         */
        
        $dummy = [];
        if($step_4_1_1 > 0){
            $dummy[0] = 1;
        }else{
            $dummy[0] = 0;
        }
        
        if($step_4_1_2 > 0){
            $dummy[1] = 1;
        }else{
            $dummy[1] = 0;
        }
        
        if($step_4_2_1 > 0){
            $dummy[2] = 1;
        }else{
            $dummy[2] = 0;
        }
        
        if($step_4_2_2 > 0){
            $dummy[3] = 1;
        }else{
            $dummy[3] = 0;
        }
        
        if($step_4_3_1 > 0){
            $dummy[4] = 1;
        }else{
            $dummy[4] = 0;
        }
        
        if($step_4_3_2 > 0){
            $dummy[5] = 1;
        }else{
            $dummy[5] = 0;
        }
        
        if($step_4_3_3 > 0){
            $dummy[6] = 1;
        }else{
            $dummy[6] = 0;
        }
        
        if($step_4_3_4 > 0){
            $dummy[7] = 1;
        }else{
            $dummy[7] = 0;
        }
        
        
        if($step_4_4_1 > 0){
            $dummy[8] = 1;
        }else{
            $dummy[8]=0;
        }
        
        if($step_4_4_2 > 0){
            $dummy[9] = 1;
        }else{
            $dummy[9] = 0;
        }
        
        if($step_4_4_3 > 0){
            $dummy[10] = 1;
        }else{
            $dummy[10] = 0;
        }
        
        if($step_4_5_1 > 0){
            $dummy[11] = 1; 
        }else{
            $dummy[11] = 0;
        }
        
        if($step_4_5_2 > 0){
            $dummy[12] = 1; 
        }else{
            $dummy[12] = 0;
        }
        
        if($step_4_5_3 > 0){
            $dummy[13] = 1; 
        }else{
            $dummy[13] = 0;
        }
       
        if($step_4_6_1 > 0){
            $dummy[14] = 1; 
        }else{
            $dummy[14] = 0;
        }
        
        if($step_4_6_2 > 0){
            $dummy[15] = 1; 
        }else{
            $dummy[15] = 0;
        }
        
        if($step_4_6_3 > 0){
            $dummy[16] = 1; 
        }else{
            $dummy[16] = 0;
        }
        
        if($step_4_7_1 > 0){
            $dummy[17] = 1; 
        }else{
            $dummy[17] = 0;
        }
        
        if($step_4_7_2 > 0){
            $dummy[18] = 1; 
        }else{
            $dummy[18] = 0;
        }
        
        if($step_4_7_3 > 0){
            $dummy[19] = 1; 
        }else{
            $dummy[19] = 0;
        }
        
       /*********** END OF STEP 6 **************/
        
        /**
         * Step 7 
         * Weigth SUM 
         */
        $step7_array  = []; 
        $table_a_1_urban = [0.0538391325301515,0.0853437151573815,0.0278576164952535,0.0593614599793962,
                            0.0376409422854523,0.0457756756658074,0.0523652466415439,0.0334961534316519,
                            0.0278576164952535,0.0539888346868783,0.0516937175755053,0.0469155962701575,
                            0.0361407241240686,0.0300653686109902,0.0467988586280607,0.0829152191373995,
                            0.0454000933069725,0.0837958701421488,0.0627205493831419,0.0360276094527852,    
                                
                            ];
        for($i=0;$i<count($table_a_1_urban);$i++){
            $step7_array[$i] = $dummy[$i]*$table_a_1_urban[$i];
        }
        
        $step7 = array_sum($step7_array); // deprivation count 
        
        /*********** END OF STEP 6 ********/
         
        /**
         * Start of step 8 
         */
     
         $hdvi =  $step_5*$step7; //
         
         /********* END OF STEP 8 ***********/
         
         /*
          * Calcul de la vulnerabilite 
          * 
          */
         $vulnerability = 0;
         
         if($hdvi >= 0.676732 && $step7 >= 0.543876){
             $vulnerability = 1;
         }
         
         elseif($hdvi >= 0.545719 && $step7 >= 0.369668){
             $vulnerability = 2;
         }
         
         elseif($hdvi >= 0.476289 && $step7 >= 0.239163){
             $vulnerability = 3;
         }
         else{
             $vulnerability = 4;
         }
         
         /**
          * Insertion du calcul finale de vulnerabilite dans la base de donnes 
          * Table vulnerabilite 
          * 
          */
         
         $famille_id = null; 
        // $commune_id = null;
         $localite_id = null;
         $commune_name = null;
         $milieu_id = null;
         $section_comm = null;
         
         foreach($famille_ as $f){
             $famille_id = $f['URI'].'_'.strtok($f['ID_HH_ADM_SEK_KOM'], '_');
             $commune_name = strtok($f['ID_HH_ADM_SEK_KOM'], '_');
             $localite_id = $f['ID_HH_A3_FINAL'];
             $milieu_id = $f['ID_HH_MILIEU_RESID'];
             $section_comm = $f['ID_HH_ADM_SEK_KOM'];
         }
         $d_indicateur = $d_1_1.','.$d_1_2.','.$d_2_1.','.$d_2_2.','
                        .$d_3_1.','.$d_3_2.','.$d_3_3.','.$d_3_4.'.'
                        .$d_4_1.','.$d_4_2.','.$d_4_3.','.$d_5_1.','
                        .$d_5_2.','.$d_5_3.','.$d_6_1.','.$d_6_2.','
                        .$d_6_3.','.$d_7_1.','.$d_7_2.','.$d_7_3.',';
         
         $prisk_indicateur = $prisk_1_1.','.$prisk_1_2.','.$prisk_2_1.','.$prisk_2_2.','
                            .$prisk_3_1.','.$prisk_3_2.','.$prisk_3_3.','.$prisk_3_4.'.'
                            .$prisk_4_1.','.$prisk_4_2.','.$prisk_4_3.','.$prisk_5_1.','
                            .$prisk_5_2.','.$prisk_5_3.','.$prisk_6_1.','.$prisk_6_2.','
                            .$prisk_6_3.','.$prisk_7_1.','.$prisk_7_2.','.$prisk_7_3.',';
                 
         $vul = new Vulnerability; 
         $vul->setAttribute('id_famille', $famille_id);
         $vul->setAttribute('commune', $commune_name);
         $vul->setAttribute('milieu', $milieu_id);
         $vul->setAttribute('localite', $localite_id);
         $vul->setAttribute('section_communale', $section_comm);
         $vul->setAttribute('d_indicator', $d_indicateur);
         $vul->setAttribute('prisk_indicator', $prisk_indicateur);
         $vul->setAttribute('hdvi', $hdvi);
         $vul->setAttribute('vulnerability', $vulnerability);
         $vul->save();
         $vul->setAttributes(null);
         
         // Destroy the variables 
         $vul = null;
         $member_famille = null;
         $member_chronique = null;
         $famille_ = null;
        
       // return $section_comm;
    }
    
    public function algoForRuralArea($uri) {
        $member_famille = $this->getMembreTable($uri);
        $member_chronique = $this->getChroniqueTable($uri);
        $famille_ = $this->getFamilleTable($uri);
        
        // Count the familly size 
        $family_size = 0;
        foreach ($member_famille as $mf) {
            $family_size++;
        }
        /**
         * Calcul de l'indicateur #1 
         * Household Demographic Composition
         */
        $count_child_under_15 = 0; // count child under 15
        $count_eldery = 0; // count eldery person >= 65 
        $is_couple = False; // Is en couple b7 == 2

        $d_1_1 = 0; // d_ variable for indicator 1
        $cat_fam; // Categorie de famille 


        foreach ($member_famille as $mf) {

            if ($mf['b3'] <= 15) { // Compte les membres de 15 ans et moins
                $count_child_under_15++;
            }

            if ($mf['b2'] >= 65) { // Compte les personnes ages de 65 ans et plus
                $count_eldery++;
            }

            if ($mf['b7'] == 2) { // Verifie qu'il a une relation de couple dans la famille
                $is_couple = True;
            }
        }

        /**
         * Classifie les familles en categorie suivant le tableau 2 du document
         */
        if ($count_child_under_15 == 0) {
            $cat_fam = 1;
        }

        if ($count_child_under_15 > 0 && $is_couple == False) {
            $cat_fam = 2;
        }

        if ($count_child_under_15 > 0 && $is_couple == True) {
            $cat_fam = 3;
        }

        if ($count_child_under_15 > 0 && $is_couple == False && $count_eldery > 0) {
            $cat_fam = 4;
        }

        if ($count_child_under_15 > 0 && $is_couple == True && $count_eldery > 0) {
            $cat_fam = 5;
        }
        /**
         * Affecte une valeur a la variable $d_1_1 suivant la categorie de la famille
         */
        switch ($cat_fam) {
            case 1:
                $d_1_1 = 0;
                break;
            case 2:
                $d_1_1 = 0.417916;
            case 3:
                $d_1_1 = 2.486249;
                break;
            case 4:
                $d_1_1 = 1.442669;
                break;
            case 5:
                $d_1_1 = 3.000000;
                break;
        }

        $prisk_1_1 = 3; // prisk_1_1 est egal a 1 page 7 du document
        $hdr_1_1 = ($d_1_1) / pow($prisk_1_1, 0.5); // Calcul de household deprivation ratio (hdr) 

        /**
         * Indicateur #2 
         * Children under 5 years old 
         */
        $count_child_0_4 = 0;
        $count_child_18_64 = 0;
        $d_1_2 = 0;

        foreach ($member_famille as $mf) {
            if ($mf['b3'] <= 4) {
                $count_child_0_4++;
            }

            if ($mf['b3'] >= 18 && $mf['b3'] <= 64) {
                $count_child_18_64++;
            }
        }

        $d_1_2 = $count_child_0_4;
        $prisk_1_2 = $count_child_18_64;
        if ($prisk_1_2 != 0) {
            $hdr_1_2 = $d_1_2 / pow($prisk_1_2, 0.5);
        } else {
            $hdr_1_2 = 0;
        }

        /**
         *  HEALTH 
         * Indicateur # 3
         * Chronically ILL 
         * 
         */
        $d_2_1 = 0;
        $count_chronically_ill = 0;

        foreach ($member_chronique as $mc) {
            if ($mc['MANM_HH1_HH_MALAD_81_E1_1_81'] == 1) {
                $count_chronically_ill++;
            }
        }

        if ($count_chronically_ill > $family_size) {
            $d_2_1 = $family_size;
        } else {
            $d_2_1 = $count_chronically_ill;
        }
        $prisk_2_1 = $family_size;
        if ($prisk_2_1 != 0) {
            $hdr_2_1 = $d_2_1 / pow($prisk_2_1, 0.5);
        } else {
            $hdr_2_1 = 0;
        }

        /**
         * Indicateur # 4
         * Disabled or permanently injured but not chronically ill
         * 
         */
        $d_2_2 = 0;
        $count_disabled = 0;

        foreach ($member_chronique as $mc) {
            if ($mc['MANM_HH1_HH_MALAD_81_E1_1_81'] == 2) {
                if ($mc['MANM_HH1_HH_MALAD_81_E3_1_81'] == 1) {
                    $count_disabled++;
                }
            }
        }

        if ($count_disabled > $family_size) {
            $d_2_2 = $family_size;
        } else {
            $d_2_2 = $count_disabled;
        }

        $prisk_2_2 = $family_size;
        if ($prisk_2_2 != 0) {
            $hdr_2_2 = $d_2_2 / pow($prisk_2_2, 0.5);
        } else {
            $hdr_2_2 = 0;
        }

        /**
         * EDUCATION 
         * Indicateur #5 
         * ILLITERACY 
         */
        $count_illiterate = 0;
        $d_3_1 = 0;
        $prisk_3_1 = 0;

        foreach ($member_famille as $mf) {
            if ($mf['b3'] >= 15) {
                if ($mf['c1'] == 2) {
                    $count_illiterate++;
                }
                $prisk_3_1++;
            }
        }

        $d_3_1 = $count_illiterate;

        if ($prisk_3_1 != 0) {
            $hdr_3_1 = $d_3_1 / pow($prisk_3_1, 0.5);
        } else {
            $hdr_3_1 = 0;
        }

        /**
         * Indicateur #6
         * Lack of basic education 
         * 
         */
        $count_not_complete_basic_edu = 0;
        $d_3_2 = 0;
        $count_member_21_plus = 0;

        foreach ($member_famille as $mf) {
            if ($mf['c1'] == 1) {
                if ($mf['b3'] >= 21 && $mf['c1_5'] <= 8) {
                    $count_not_complete_basic_edu++;
                }
            }
            if ($mf['b3'] >= 21) {
                $count_member_21_plus++;
            }
        }

        $d_3_2 = $count_not_complete_basic_edu;
        $prisk_3_2 = $count_member_21_plus;

        if ($prisk_3_2 != 0) {
            $hdr_3_2 = $d_3_2 / pow($prisk_3_2, 0.5);
        } else {
            $hdr_3_2 = 0;
        }

        /**
         * 
         * Indicateur #7 
         * School non-attendance 
         * 
         */
        $count_member_not_at_school = 0;
        $d_3_3 = 0;
        $count_member_schooling_age = 0;
        foreach ($member_famille as $mf) {
            if ($mf['b3'] >= 3 && $mf['b3'] <= 18) {
                $count_member_schooling_age++;
                if ($mf['c1_3'] == 2) {
                    $count_member_not_at_school++;
                }
            }
        }

        $d_3_3 = $count_member_not_at_school;
        $prisk_3_3 = $count_member_schooling_age;

        if ($prisk_3_3 != 0) {
            $hdr_3_3 = $d_3_3 / pow($prisk_3_3, 0.5);
        } else {
            $hdr_3_3 = 0;
        }

        /**
         * Indicateur 8 
         * School LAG
         * 
         */
        $d_3_4 = 0;
        $expect_year_e;
        $gap = [];
        $i = 0;
        $count_member_3_20 = 0;
        foreach ($member_famille as $mf) {
            if ($mf['b3'] >= 3 && $mf['b3'] <= 20) {
                $count_member_3_20++;

                if ($mf['b3'] < 7) {
                    $expect_year_e = 0;
                } else {
                    switch ($mf['b3']) {
                        case 7:
                            $expect_year_e = 1;
                            break;
                        case 8:
                            $expect_year_e = 2;
                            break;
                        case 9:
                            $expect_year_e = 3;
                            break;
                        case 10:
                            $expect_year_e = 4;
                            break;
                        case 11:
                            $expect_year_e = 5;
                            break;
                        case 12:
                            $expect_year_e = 6;
                            break;
                        case 13:
                            $expect_year_e = 7;
                            break;
                        case 14:
                            $expect_year_e = 8;
                            break;
                        case 15:
                            $expect_year_e = 9;
                            break;
                        case 16:
                            $expect_year_e = 10;
                            break;
                        case 17:
                            $expect_year_e = 11;
                            break;
                        case 18:
                            $expect_year_e = 12;
                            break;
                    }
                    if ($mf['b3'] > 18) {
                        $expect_year_e = 13;
                    }
                }

                // GAP calculation 
                if ($expect_year_e == 0) {
                    $gap[$i] = 0;
                    $i++;
                } else {
                    $var_gap = $expect_year_e - $mf['c1_5'];
                    if ($var_gap < 0) {
                        $gap[$i] = 0;
                        $i++;
                    } else {
                        $gap[$i] = $var_gap;
                        $i++;
                    }
                }
            }
        }

        // Analyse the gap
        $result_gap = [];
        $j = 0;
        for ($x = 0; $x < count($gap); $x++) {
            if ($gap[$x] == 0) {
                $result_gap[$j] = 0;
                $j++;
            }

            if ($gap[$x] > 0 && $gap[$x] <= 3) {
                $result_gap[$j] = 1;
                $j++;
            }

            if ($gap[$x] >= 4) {
                $result_gap[$j] = 2;
                $j++;
            }
        }

        $total_count = array_sum($result_gap);

        if ($total_count > $count_member_3_20) {
            $d_3_4 = $count_member_3_20;
        } else {
            $d_3_4 = $total_count;
        }

        $prisk_3_4 = $count_member_3_20;

        if ($prisk_3_4 != 0) {
            $hdr_3_4 = $d_3_4 / pow($prisk_3_4, 0.5);
        } else {
            $hdr_3_4 = 0;
        }


        /**
         * LABOUR Condition 
         * Inactivity
         * Indicateur #9 
         * 
         */
        $d_4_1 = 0;
        $count_member_inactive = 0;
        $count_member_active = 0;
        $count_member_active_inactive = 0;
        $active_array = [1, 2, 3, 4, 5, 6, 8, 9];
        $inactive_array = [7, 10, 11, 12];
        foreach ($member_famille as $mf) {
            if ($mf['b3'] >= 18 && $mf['b3'] <= 64) {
                $count_member_active_inactive++;
                if (in_array($mf['d5'], $active_array)) {
                    $count_member_active++;
                }
                if (in_array($mf['d5'], $inactive_array)) {
                    $count_member_inactive++;
                }
            }
        }

        $d_4_1 = $count_member_inactive;
        $prisk_4_1 = $count_member_active_inactive;
        if ($prisk_4_1 != 0) {
            $hdr_4_1 = $d_4_1 / pow($prisk_4_1, 0.5);
        } else {
            $hdr_4_1 = 0;
        }

        /**
         * Unemployment 
         * Indicateur #10
         *
         */
        $count_member_unemployed = 0;
        $d_4_2 = 0;

        foreach ($member_famille as $mf) {
            if (in_array($mf['d5'], $active_array) && ($mf['b3'] >= 18 && $mf['b3'] <= 64)) {
                if ($mf['d2'] == 2) {
                    $count_member_unemployed++;
                }
            }
        }
        $d_4_2 = $count_member_unemployed;
        $prisk_4_2 = $count_member_active;

        if ($prisk_4_2 != 0) {
            $hdr_4_2 = $d_4_2 / pow($prisk_4_2, 0.5);
        } else {
            $hdr_4_2 = 0;
        }

        /**
         * Child Labor 
         * Indicateur #11 
         * 
         */
        $count_child_labourer_1 = 0;
        $count_child_labourer_2 = 0;
        $d_4_3 = 0; 
        
        foreach ($member_famille as $mf) {
            if(in_array($mf['d5'],$active_array) && ($mf['b3'] >= 10 && $mf['b3'] <= 12) ) {
                $count_child_labourer_1++;
            }
            if(in_array($mf['d5'],$active_array) && ($mf['b3'] >= 13 && $mf['b3'] <= 15) ) {
                $count_child_labourer_2++;
            }
        }
        
        $d_4_3 = $count_child_labourer_1*1.5 + $count_child_labourer_2; 
        $prisk_4_3 = $count_child_labourer_1*1.5 + $count_child_labourer_2;

        if($prisk_4_3 != 0){
            $hdr_4_3 = $d_4_3/pow($prisk_4_3,0.5);
        }
        else{
            $hdr_4_3 = 0;
        }
        
        /**
         * FOOD SECURITY 
         * Absence of food 
         * Indicator #12
         * 
         */
        
        $d_5_1 = 0;
        $prisk_5_1 = 10;
        foreach($famille_ as $f){
            switch($f['H24']){
                case 0:
                    $d_5_1 = 0;
                    break;
                case 1:
                    $d_5_1 = 3;
                    break;
                case 2:
                    $d_5_1 = 10;
                    break;
            }
        }
        
        $hdr_5_1 = $d_5_1/pow($prisk_5_1,0.5);
        
        /*
         * Hunger 
         * Indicator #13 
         */
        
        $d_5_2 = 0;
        $prisk_5_2 = 10;
        
        foreach($famille_ as $f){
            
            switch($f['H25']){
                case 0:
                    $d_5_2 = 0;
                    break;
                case 1:
                    $d_5_2 = 3;
                    break;
                case 2:
                    $d_5_2 = 10;
                    break;
            }
        }
        
         $hdr_5_2 = $d_5_2/pow($prisk_5_2,0.5);
         
         /**
          * Restricted Consumption
          * Indicator #14 
          */
        
        $d_5_3 = 0;
        $prisk_5_3 = 10;
        
        foreach($famille_ as $f){
            
            switch($f['H26']){
                case 0:
                    $d_5_3 = 0;
                    break;
                case 1:
                    $d_5_3 = 3;
                    break;
                case 2:
                    $d_5_3 = 10;
                    break;
            }
        }
        
        $hdr_5_3 = $d_5_3/pow($prisk_5_3,0.5);
        
        /**
         * Ressource at Home 
         * Absence of remittances or Benefits 
         * Indicator #15 
         */
        $d_6_1 = 0;
        $prisk_6_1 = 0;
        $hdr_6_1 = 0; // This variable is not considering in the final calculation due to lack of information in the "Formulaire d'enquete"
        
        
        /**
         * Dwelling conditions 
         * INidcator #16 
         * 
         */
        
        $d_6_2 = 0;
        $prisk_6_2 = 3; 
        $cat_floor_1 = [1,2,6];
        $cat_roof_1 = [1,2,3,4];
        $cat_wall_1 = [1,2,3,4,7,8];
        $floor = 0;
        $wall = 0;
        $roof = 0;
        foreach($famille_ as $f){
            // Determine the floor material
            if(in_array($f['BIENS_HH_F9'], $cat_floor_1)){
                $floor = 1;
            }
            else {
                $floor = 0;
            }
            
            if(in_array($f['BIENS_HH_F8_2'],$cat_wall_1)){
                $wall = 1;
            }
            else{
                $wall = 0;
            }
            
            if(in_array($f['BIENS_HH_F8_1'],$cat_roof_1)){
                $roof = 1;
            }
            else{
                $roof = 0;
            }
            
        }
        $d_6_2 = ($floor*0.5534845 + $wall*0.3674259 + $roof*0.0790896)*3;
        $hdr_6_2 = $d_6_2/pow($prisk_6_2,0.5); 
        
        /**
         * 
         * Overcrowding  
         * Indicator : 17
         */
        
        $number_of_romm = 0;
        $ratio_room_member = 0;
        $d_6_3 = 0;
        $prisk_6_3 = 10;
        foreach($famille_ as $f){
            $number_of_romm = $f['BIENS_HH_F6'];
        }
        
        if($number_of_romm != 0 && $number_of_romm != null && is_int($number_of_romm) ){
            $ratio_room_member = $family_size/$number_of_romm; 
        }
        
        if($ratio_room_member > 10){
            $d_6_3 = 10;
        }
        elseif($ratio_room_member < 4.5){
            $d_6_3 = 0;
        }else{
            $d_6_3 = $ratio_room_member;
        }
        
        $hdr_6_3 = $d_6_3/pow($prisk_6_3,0.5);
        
        /**
         * Deprived Lighting Access 
         * Indicator #18 
         * 
         */
        
        $d_7_1 = 0; 
        $prisk_7_1 = 2; 
        $dum_1_7_1 = 0;
        $dum_2_7_1 = 0;
        $limye = [1,2,3];
        $dife = [1,2];
        foreach($famille_ as $f){
            if(in_array($f['BIENS_HH_F9_1'],$limye)){
                $dum_1_7_1 = 1;
            }else{
                $dum_1_7_1 = 0;
            }
            
            if(in_array($f['BIENS_HH_F11'],$dife)){
                $dum_2_7_1 = 1;
            }else{
                $dum_2_7_1 = 0;
            }
            
        }
        
        $d_7_1 = $dum_1_7_1*0.35821529 + $dum_2_7_1*0.64178471;
        
        $hdr_7_1 = $d_7_1/pow($prisk_7_1,0.5);
        
        
        /*
         * Deprived Access to water 
         * Indicator #19 
         */
       
        $d_7_2 = 0;
        $prisk_7_2 = 2; 
        $dum_1_7_2 = 0;
        $dum_2_7_2 = 0;
        //$dlo_potab = [];
        $dlo_sevi = [1,2,4,5];
        
        foreach($famille_ as $f){
            // For potable water in urban area 
         if($f['BIENS_HH_F15_0']!=8){
             $dum_1_7_2 = 1;
         }else{
             $dum_1_7_2 = 0;
         }
         // For cleaning water in urban area
         if(in_array($f['BIENS_HH_F15'], $dlo_sevi)){
             $dum_2_7_2 = 1;
         }else{
             $dum_2_7_2 = 0;
         }
         
        }
        
        $d_7_2 = $dum_1_7_2*0.67586683 + $dum_2_7_2*0.32413317;
        $hdr_7_2 = $d_7_2/pow($prisk_7_2,0.5);
        
        /*
         * Deprived sanitation conditions 
         * Indicator # 20 
         */
        
        $d_7_3 = 0;
        $prisk_7_3 = 2;
        $dum_1_7_3 = 0;
        $dum_2_7_3 = 0;
        $twalet = [1,2];
        $fatra = [2,3,6,7];
        
        foreach($famille_ as $f){
           if(in_array($f['BIENS_HH_F10'], $twalet)){
               $dum_1_7_3 = 1;
           } else{
               $dum_1_7_3;
           }
           
           if(in_array($f['SEVIS_KONPOTMAN_HH_I5_2'], $fatra)){
               $dum_2_7_3 = 1;
           }else{
               $dum_2_7_3 = 0;
           }
        }
        
        $d_7_3 = $dum_1_7_3*0.87169304 + $dum_2_7_3*0.12830696;
        
        $hdr_7_3 = $d_7_3/pow($prisk_7_3,0.5);
        
       
        
        /**** Make calculation from step 2 to 8 *
         *  During this step the variable will name as stepi_j_k
         *  where i = the value of the step 
         * j = the category of indicator 
         * k = the number of the indicator
         */
        
        
        /*
         * Step 3 : Elevate all $hdr_i_j to the power of beta 
         */
        $beta = 0.8;
        $step_3_1_1 = pow($hdr_1_1,$beta); //1
        $step_3_1_2 = pow($hdr_1_2,$beta); //2
        
        $step_3_2_1 = pow($hdr_2_1,$beta); //3
        $step_3_2_2 = pow($hdr_2_2,$beta); //4
        
        $step_3_3_1 = pow($hdr_3_1,$beta); //5
        $step_3_3_2 = pow($hdr_3_2,$beta); //6
        $step_3_3_3 = pow($hdr_3_3,$beta); //7
        $step_3_3_4 = pow($hdr_3_4,$beta); //8
        
        $step_3_4_1 = pow($hdr_4_1,$beta); //9
        $step_3_4_2 = pow($hdr_4_2,$beta); //10
        $step_3_4_3 = pow($hdr_4_3,$beta); //11
        
        $step_3_5_1 = pow($hdr_5_1,$beta); // 12
        $step_3_5_2 = pow($hdr_5_2,$beta); // 13
        $step_3_5_3 = pow($hdr_5_3,$beta); // 14
        
        $step_3_6_1 = pow($hdr_6_1,$beta); // 15
        $step_3_6_2 = pow($hdr_6_2,$beta); // 16
        $step_3_6_3 = pow($hdr_6_3,$beta); // 17
        
        $step_3_7_1 = pow($hdr_7_1,$beta); // 18
        $step_3_7_2 = pow($hdr_7_2,$beta); // 19
        $step_3_7_3 = pow($hdr_7_3,$beta); // 20
        
        /**** End of Step 3 *****/
        
        /*
         * Start of step 4 
         * Multiply by the weight in table A3 
         * 
         */
        $step_4_1_1 = $step_3_1_1*0.0429314994696670; 
        $step_4_1_2 = $step_3_1_2*0.0902619457321613;
        
        $step_4_2_1 = $step_3_2_1*0.0492349223596979;
        $step_4_2_2 = $step_3_2_2*0.0245334959872276;
        
        $step_4_3_1 = $step_3_3_1*0.0405288909750612;
        $step_4_3_2 = $step_3_3_2*0.0560308397563253;
        $step_4_3_3 = $step_3_3_3*0.0509274346660841;
        $step_4_3_4 = $step_3_3_4*0.0617162252859970;
        
        $step_4_4_1 = $step_3_4_1*0.0402014273313146;
        $step_4_4_2 = $step_3_4_2*0.0528897275638515;
        $step_4_4_3 = $step_3_4_3*0.0410536216067491;
        
        $step_4_5_1 = $step_3_5_1*0.0494011139694204;
        $step_4_5_2 = $step_3_5_2*0.0245334959872276;
        $step_4_5_3 = $step_3_5_3*0.0259645459513483;
        
        $step_4_6_1 = $step_3_6_1*0.0438113000009370;
        $step_4_6_2 = $step_3_6_2*0.0633357834303214;
        $step_4_6_3 = $step_3_6_3*0.0479693955686851;
        
        $step_4_7_1 = $step_3_7_1*0.0769524308370264;
        $step_4_7_2 = $step_3_7_2*0.0777533690769001;
        $step_4_7_3 = $step_3_7_3*0.0630765423973395;
        
        /************* END OF STEP 4  ****************/
        
        /**
         * Start step 5 
         * Sum the 20 indicator obtain in step 4 
         */
        
        $sigmaStep_5 = $step_4_1_1 + $step_4_1_2 + $step_4_2_1 + $step_4_2_2 + $step_4_3_1 + $step_4_3_2 + $step_4_3_3 + $step_4_3_4 + $step_4_4_1 + $step_4_4_2 + $step_4_4_3 + $step_4_5_1 + $step_4_5_2 + $step_4_5_3 + $step_4_6_1 + $step_4_6_2 + $step_4_6_3 + $step_4_7_1 + $step_4_7_2 + $step_4_7_3; 
        $step_5 = pow($sigmaStep_5,1/$beta); 
        
        /********** END OF STEP 5 ********************/
        
        /**
         * Start step 6
         * 
         */
        
        $dummy = [];
        if($step_4_1_1 > 0){
            $dummy[0] = 1;
        }else{
            $dummy[0] = 0;
        }
        
        if($step_4_1_2 > 0){
            $dummy[1] = 1;
        }else{
            $dummy[1] = 0;
        }
        
        if($step_4_2_1 > 0){
            $dummy[2] = 1;
        }else{
            $dummy[2] = 0;
        }
        
        if($step_4_2_2 > 0){
            $dummy[3] = 1;
        }else{
            $dummy[3] = 0;
        }
        
        if($step_4_3_1 > 0){
            $dummy[4] = 1;
        }else{
            $dummy[4] = 0;
        }
        
        if($step_4_3_2 > 0){
            $dummy[5] = 1;
        }else{
            $dummy[5] = 0;
        }
        
        if($step_4_3_3 > 0){
            $dummy[6] = 1;
        }else{
            $dummy[6] = 0;
        }
        
        if($step_4_3_4 > 0){
            $dummy[7] = 1;
        }else{
            $dummy[7] = 0;
        }
        
        
        if($step_4_4_1 > 0){
            $dummy[8] = 1;
        }else{
            $dummy[8]=0;
        }
        
        if($step_4_4_2 > 0){
            $dummy[9] = 1;
        }else{
            $dummy[9] = 0;
        }
        
        if($step_4_4_3 > 0){
            $dummy[10] = 1;
        }else{
            $dummy[10] = 0;
        }
        
        if($step_4_5_1 > 0){
            $dummy[11] = 1; 
        }else{
            $dummy[11] = 0;
        }
        
        if($step_4_5_2 > 0){
            $dummy[12] = 1; 
        }else{
            $dummy[12] = 0;
        }
        
        if($step_4_5_3 > 0){
            $dummy[13] = 1; 
        }else{
            $dummy[13] = 0;
        }
       
        if($step_4_6_1 > 0){
            $dummy[14] = 1; 
        }else{
            $dummy[14] = 0;
        }
        
        if($step_4_6_2 > 0){
            $dummy[15] = 1; 
        }else{
            $dummy[15] = 0;
        }
        
        if($step_4_6_3 > 0){
            $dummy[16] = 1; 
        }else{
            $dummy[16] = 0;
        }
        
        if($step_4_7_1 > 0){
            $dummy[17] = 1; 
        }else{
            $dummy[17] = 0;
        }
        
        if($step_4_7_2 > 0){
            $dummy[18] = 1; 
        }else{
            $dummy[18] = 0;
        }
        
        if($step_4_7_3 > 0){
            $dummy[19] = 1; 
        }else{
            $dummy[19] = 0;
        }
        
       /*********** END OF STEP 6 **************/
        
        /**
         * Step 7 
         * Weigth SUM 
         */
        $step7_array  = []; 
        $table_a_1_urban = [0.0538391325301515,0.0853437151573815,0.0278576164952535,0.0593614599793962,
                            0.0376409422854523,0.0457756756658074,0.0523652466415439,0.0334961534316519,
                            0.0278576164952535,0.0539888346868783,0.0516937175755053,0.0469155962701575,
                            0.0361407241240686,0.0300653686109902,0.0467988586280607,0.0829152191373995,
                            0.0454000933069725,0.0837958701421488,0.0627205493831419,0.0360276094527852,    
                                
                            ];
        for($i=0;$i<count($table_a_1_urban);$i++){
            $step7_array[$i] = $dummy[$i]*$table_a_1_urban[$i];
        }
        
        $step7 = array_sum($step7_array); // deprivation count 
        
        /*********** END OF STEP 6 ********/
         
        /**
         * Start of step 8 
         */
     
         $hdvi =  $step_5*$step7; //
         
         /********* END OF STEP 8 ***********/
         
         /*
          * Calcul de la vulnerabilite 
          * 
          */
         $vulnerability = 0;
         
         if($hdvi >= 0.676732 && $step7 >= 0.543876){
             $vulnerability = 1;
         }
         
         elseif($hdvi >= 0.545719 && $step7 >= 0.369668){
             $vulnerability = 2;
         }
         
         elseif($hdvi >= 0.476289 && $step7 >= 0.239163){
             $vulnerability = 3;
         }
         else{
             $vulnerability = 4;
         }
         
         /**
          * Insertion du calcul finale de vulnerabilite dans la base de donnes 
          * Table vulnerabilite 
          * 
          */
         
         $famille_id = null; 
        // $commune_id = null;
         $localite_id = null;
         $commune_name = null;
         $milieu_id = null;
         $section_comm = null;
         
         foreach($famille_ as $f){
             $famille_id = $f['URI'].'_'.strtok($f['ID_HH_ADM_SEK_KOM'], '_');
             $commune_name = strtok($f['ID_HH_ADM_SEK_KOM'], '_');
             $localite_id = $f['ID_HH_A3_FINAL'];
             $milieu_id = $f['ID_HH_MILIEU_RESID'];
             $section_comm = $f['ID_HH_ADM_SEK_KOM'];
         }
         $d_indicateur = $d_1_1.','.$d_1_2.','.$d_2_1.','.$d_2_2.','
                        .$d_3_1.','.$d_3_2.','.$d_3_3.','.$d_3_4.'.'
                        .$d_4_1.','.$d_4_2.','.$d_4_3.','.$d_5_1.','
                        .$d_5_2.','.$d_5_3.','.$d_6_1.','.$d_6_2.','
                        .$d_6_3.','.$d_7_1.','.$d_7_2.','.$d_7_3.',';
         
         $prisk_indicateur = $prisk_1_1.','.$prisk_1_2.','.$prisk_2_1.','.$prisk_2_2.','
                            .$prisk_3_1.','.$prisk_3_2.','.$prisk_3_3.','.$prisk_3_4.'.'
                            .$prisk_4_1.','.$prisk_4_2.','.$prisk_4_3.','.$prisk_5_1.','
                            .$prisk_5_2.','.$prisk_5_3.','.$prisk_6_1.','.$prisk_6_2.','
                            .$prisk_6_3.','.$prisk_7_1.','.$prisk_7_2.','.$prisk_7_3.',';
                 
         $vul = new Vulnerability; 
         $vul->setAttribute('id_famille', $famille_id);
         $vul->setAttribute('commune', $commune_name);
         $vul->setAttribute('milieu', $milieu_id);
         $vul->setAttribute('localite', $localite_id);
         $vul->setAttribute('section_communale', $section_comm);
         $vul->setAttribute('d_indicator', $d_indicateur);
         $vul->setAttribute('prisk_indicator', $prisk_indicateur);
         $vul->setAttribute('hdvi', $hdvi);
         $vul->setAttribute('vulnerability', $vulnerability);
         $vul->save();
         $vul->setAttributes(null);
         
         // Destroy the variables 
         $vul = null;
         $member_famille = null;
         $member_chronique = null;
         $famille_ = null;
         /*
         $vul = null; // Detruit l'object 
       
         // Set les variables finales a null 
         $d_indicateur = null;
         $prisk_indicateur = null;
          * 
          */
        
        
        //return $section_comm;
    }
    
    
    public function insertVulnerability(){
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        $fam = new Famille;
        $sql = "SELECT URI, ID_HH_MILIEU_RESID FROM famille";
        $fa_ = $fam->findBySql($sql)->asArray()->all();
        $total = count($fa_);
        $count = 0;
     //  $count = 0;
        foreach($fa_ as $f){
            $count++;
            $uri = $f['URI'];
            $milieu = $f['ID_HH_MILIEU_RESID'];
           
            if($milieu == 2){
                $this->algoForUrbanArea($uri);
                flush();
                ob_flush();
            }elseif($milieu == 1){
                $this->algoForRuralArea($uri);
                flush();
                ob_flush();
            }
           
        }
    }

}

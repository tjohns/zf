<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Measure
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Implement basic abstract class
 */
require_once 'Zend/Measure/Abstract.php';

/**
 * Implement Locale Data and Format class
 */
require_once 'Zend/Locale.php';
require_once 'Zend/Locale/Data.php';
require_once 'Zend/Locale/Format.php';


/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Weigth
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Weight extends Zend_Measure_Abstract
{
    // Weight definitions
    const STANDARD = 'Weight::KILOGRAM';

    const ARRATEL               = 'Weight::ARRATEL';
    const ARTEL                 = 'Weight::ARTEL';
    const ARROBA_PORTUGUESE     = 'Weight::ARROBA_PORTUGUESE';
    const ARROBA                = 'Weight::ARROBA';
    const AS_                   = 'Weight::AS_';
    const ASS                   = 'Weight::ASS';
    const ATOMIC_MASS_UNIT_1960 = 'Weight::ATOMIC_MASS_UNIT_1960';
    const ATOMIC_MASS_UNIT_1973 = 'Weight::ATOMIC_MASS_UNIT_1973';
    const ATOMIC_MASS_UNIT_1986 = 'Weight::ATOMIC_MASS_UNIT_1986';
    const ATOMIC_MASS_UNIT      = 'Weight::ATOMIC_MASS_UNIT';
    const AVOGRAM               = 'Weight::AVOGRAM';
    const BAG                   = 'Weight::BAG';
    const BAHT                  = 'Weight::BAHT';
    const BALE                  = 'Weight::BALE';
    const BALE_US               = 'Weight::BALE_US';
    const BISMAR_POUND          = 'Weight::BISMAR_POUND';
    const CANDY                 = 'Weight::CANDY';
    const CARAT_INTERNATIONAL   = 'Weight::CARAT_INTERNATIONAL';
    const CARAT                 = 'Weight::CARAT';
    const CARAT_UK              = 'Weight::CARAT_UK';
    const CARAT_US_1913         = 'Weight::CARAT_US_1913';
    const CARGA                 = 'Weight::CARGA';
    const CATTI                 = 'Weight::CATTI';
    const CATTI_JAPANESE        = 'Weight::CATTI_JAPANESE';
    const CATTY                 = 'Weight::CATTY';
    const CATTY_JAPANESE        = 'Weight::CATTY_JAPANESE';
    const CATTY_THAI            = 'Weight::CATTY_THAI';
    const CENTAL                = 'Weight::CENTAL';
    const CENTIGRAM             = 'Weight::CENTIGRAM';
    const CENTNER               = 'Weight::CENTNER';
    const CENTNER_RUSSIAN       = 'Weight::CENTNER_RUSSIAN';
    const CHALDER               = 'Weight::CHALDER';
    const CHALDRON              = 'Weight::CHALDRON';
    const CHIN                  = 'Weight::CHIN';
    const CHIN_JAPANESE         = 'Weight::CHIN_JAPANESE';
    const CLOVE                 = 'Weight::CLOVE';
    const CRITH                 = 'Weight::CRITH';
    const DALTON                = 'Weight::DALTON';
    const DAN                   = 'Weight::DAN';
    const DAN_JAPANESE          = 'Weight::DAN_JAPANESE';
    const DECIGRAM              = 'Weight::DECIGRAM';
    const DECITONNE             = 'Weight::DECITONNE';
    const DEKAGRAM              = 'Weight::DEKAGRAM';
    const DEKATONNE             = 'Weight::DEKATONNE';
    const DENARO                = 'Weight::DENARO';
    const DENIER                = 'Weight::DENIER';
    const DRACHME               = 'Weight::DRACHME';
    const DRAM                  = 'Weight::DRAM';
    const DRAM_APOTHECARIES     = 'Weight::DRAM_APOTHECARIES';
    const DYNE                  = 'Weight::DYNE';
    const ELECTRON              = 'Weight::ELECTRON';
    const ELECTRONVOLT          = 'Weight::ELECTRONVOLT';
    const ETTO                  = 'Weight::ETTO';
    const EXAGRAM               = 'Weight::EXAGRAM';
    const FEMTOGRAM             = 'Weight::FEMTOGRAM';
    const FIRKIN                = 'Weight::FIRKIN';
    const FLASK                 = 'Weight::FLASK';
    const FOTHER                = 'Weight::FOTHER';
    const FOTMAL                = 'Weight::FOTMAL';
    const FUNT                  = 'Weight::FUNT';
    const FUNTE                 = 'Weight::FUNTE';
    const GAMMA                 = 'Weight::GAMMA';
    const GIGAELECTRONVOLT      = 'Weight::GIGAELECTRONVOLT';
    const GIGAGRAM              = 'Weight::GIGAGRAM';
    const GIGATONNE             = 'Weight::GIGATONNE';
    const GIN                   = 'Weight::GIN';
    const GIN_JAPANESE          = 'Weight::GIN_JAPANESE';
    const GRAIN                 = 'Weight::GRAIN';
    const GRAM                  = 'Weight::GRAM';
    const GRAN                  = 'Weight::GRAN';
    const GRANO                 = 'Weight::GRANO';
    const GRANI                 = 'Weight::GRANI';
    const GROS                  = 'Weight::GROS';
    const HECTOGRAM             = 'Weight::HECTOGRAM';
    const HUNDRETWEIGHT         = 'Weight::HUNDRETWEIGHT';
    const HUNDRETWEIGHT_US      = 'Weight::HUNDRETWEIGHT_US';
    const HYL                   = 'Weight::HYL';
    const JIN                   = 'Weight::JIN';
    const JUPITER               = 'Weight::JUPITER';
    const KATI                  = 'Weight::KATI';
    const KATI_JAPANESE         = 'Weight::KATI_JAPANESE';
    const KEEL                  = 'Weight::KEEL';
    const KEG                   = 'Weight::KEG';
    const KILODALTON            = 'Weight::KILODALTON';
    const KILOGRAM              = 'Weight::KILOGRAM';
    const KILOGRAM_FORCE        = 'Weight::KILOGRAM_FORCE';
    const KILOTON               = 'Weight::KILOTON';
    const KILOTON_US            = 'Weight::KILOTON_US';
    const KILOTONNE             = 'Weight::KILOTONNE';
    const KIN                   = 'Weight::KIN';
    const KIP                   = 'Weight::KIP';
    const KOYAN                 = 'Weight::KOYAN';
    const KWAN                  = 'Weight::KWAN';
    const LAST_GERMANY          = 'Weight::LAST_GERMANY';
    const LAST                  = 'Weight::LAST';
    const LAST_WOOL             = 'Weight::LAST_WOOL';
    const LB                    = 'Weight::LB';
    const LBS                   = 'Weight::LBS';
    const LIANG                 = 'Weight::LIANG';
    const LIBRA_ITALIAN         = 'Weight::LIBRE_ITALIAN';
    const LIBRA_SPANISH         = 'Weight::LIBRA_SPANISH';
    const LIBRA_PORTUGUESE      = 'Weight::LIBRA_PORTUGUESE';
    const LIBRA_ANCIENT         = 'Weight::LIBRA_ANCIENT';
    const LIBRA                 = 'Weight::LIBRA';
    const LIVRE                 = 'Weight::LIVRE';
    const LONG_TON              = 'Weight::LONG_TON';
    const LOT                   = 'Weight::LOT';
    const MACE                  = 'Weight::MACE';
    const MAHND                 = 'Weight::MAHND';
    const MARC                  = 'Weight::MARC';
    const MARCO                 = 'Weight::MARCO';
    const MARK                  = 'Weight::MARK';
    const MARK_GERMAN           = 'Weight::MARK_GERMANY';
    const MAUND                 = 'Weight::MAUND';
    const MAUND_PAKISTAN        = 'Weight::MAUND_PAKISTAN';
    const MEGADALTON            = 'Weight::MEGADALTON';
    const MEGAGRAM              = 'Weight::MEGAGRAM';
    const MEGATONNE             = 'Weight::MEGATONNE';
    const MERCANTILE_POUND      = 'Weight::MERCANTILE_POUND';
    const METRIC_TON            = 'Weight::METRIC_TON';
    const MIC                   = 'Weight::MIC';
    const MICROGRAM             = 'Weight::MICROGRAM';
    const MILLIDALTON           = 'Weight::MILLIDALTON';
    const MILLIER               = 'Weight::MILLIER';
    const MILLIGRAM             = 'Weight::MILLIGRAM';
    const MILLIMASS_UNIT        = 'Weight::MILLIMASS_UNIT';
    const MINA                  = 'Weight::MINA';
    const MOMME                 = 'Weight::MOMME';
    const MYRIAGRAM             = 'Weight::MYRIAGRAM';
    const NANOGRAM              = 'Weight::NANOGRAM';
    const NEWTON                = 'Weight::NEWTON';
    const OBOL                  = 'Weight::OBOL';
    const OBOLOS                = 'Weight::OBOLOS';
    const OBOLUS                = 'Weight::OBOLUS';
    const OBOLOS_ANCIENT        = 'Weight::OBOLOS_ANCIENT';
    const OBOLUS_ANCIENT        = 'Weight::OBOLUS_ANCIENT';
    const OKA                   = 'Weight::OKA';
    const ONCA                  = 'Weight::ONCA';
    const ONCE                  = 'Weight::ONCE';
    const ONCIA                 = 'Weight::ONCIA';
    const ONZA                  = 'Weight::ONZA';
    const ONS                   = 'Weight::ONS';
    const OUNCE                 = 'Weight::OUNCE';
    const OUNCE_FORCE           = 'Weight::OUNCE_FORCE';
    const OUNCE_TROY            = 'Weight::OUNCE_TROY';
    const PACKEN                = 'Weight::PACKEN';
    const PENNYWEIGHT           = 'Weight::PENNYWEIGHT';
    const PETAGRAM              = 'Weight::PETAGRAM';
    const PFUND                 = 'Weight::PFUND';
    const PICOGRAM              = 'Weight::PICOGRAM';
    const POINT                 = 'Weight::POINT';
    const POND                  = 'Weight::POND';
    const POUND                 = 'Weight::POUND';
    const POUND_FORCE           = 'Weight::POUND_FORCE';
    const POUND_METRIC          = 'Weight::POUND_METRIC';
    const POUND_TROY            = 'Weight::POUND_TROY';
    const PUD                   = 'Weight::PUD';
    const POOD                  = 'Weight::POOD';
    const PUND                  = 'Weight::PUND';
    const QIAN                  = 'Weight::QIAN';
    const QINTAR                = 'Weight::QINTAR';
    const QUARTER               = 'Weight::QUARTER';
    const QUARTER_US            = 'Weight::QUARTER_US';
    const QUARTER_TON           = 'Weight::QUARTER_TON';
    const QUARTERN              = 'Weight::QUARTERN';
    const QUARTERN_LOAF         = 'Weight::QUARTERN_LOAF';
    const QUINTAL_FRENCH        = 'Weight::QUINTAL_FRENCH';
    const QUINTAL               = 'Weight::QUINTAL';
    const QUINTAL_PORTUGUESE    = 'Weight::QUINTAL_PORTUGUESE';
    const QUINTAL_SPAIN         = 'Weight::QUINTAL_SPAIN';
    const REBAH                 = 'Weight::REBAH';
    const ROTL                  = 'Weight::ROTL';
    const ROTEL                 = 'Weight::ROTEL';
    const ROTTLE                = 'Weight::ROTTLE';
    const RATEL                 = 'Weight::RATEL';
    const SACK                  = 'Weight::SACK';
    const SCRUPLE               = 'Weight::SCRUPLE';
    const SEER                  = 'Weight::SEER';
    const SEER_PAKISTAN         = 'Weight::SEER_PAKISTAN';
    const SHEKEL                = 'Weight::SHEKEL';
    const SHORT_TON             = 'Weight::SHORT_TON';
    const SLINCH                = 'Weight::SLINCH';
    const SLUG                  = 'Weight::SLUG';
    const STONE                 = 'Weight::STONE';
    const TAEL                  = 'Weight::TAEL';
    const TAHIL_JAPANESE        = 'Weight::TAHIL_JAPANESE';
    const TAHIL                 = 'Weight::TAHIL';
    const TALENT                = 'Weight::TALENT';
    const TAN                   = 'Weight::TAN';
    const TECHNISCHE_MASS_EINHEIT = 'Weight::TECHNISCHE_MASS_EINHEIT';
    const TERAGRAM              = 'Weight::TERAGRAM';
    const TETRADRACHM           = 'Weight::TETRADRACHM';
    const TICAL                 = 'Weight::TICAL';
    const TOD                   = 'Weight::TOD';
    const TOLA                  = 'Weight::TOLA';
    const TOLA_PAKISTAN         = 'Weight::TOLA_PAKISTAN';
    const TON_UK                = 'Weight::TON_UK';
    const TON                   = 'Weight::TON';
    const TON_US                = 'Weight::TON_US';
    const TONELADA_PORTUGUESE   = 'Weight::TONELADA_PORTUGUESE';
    const TONELADA              = 'Weight::TONELADA';
    const TONNE                 = 'Weight::TONNE';
    const TONNEAU               = 'Weight::TONNEAU';
    const TOVAR                 = 'Weight::TOVAR';
    const TROY_OUNCE            = 'Weight::TROY_OUNCE';
    const TROY_POUND            = 'Weight::TROY_POUND';
    const TRUSS                 = 'Weight::TRUSS';
    const UNCIA                 = 'Weight::UNCIA';
    const UNZE                  = 'Weight::UNZE';
    const VAGON                 = 'Weight::VAGON';
    const YOCTOGRAM             = 'Weight::YOCTOGRAM';
    const YOTTAGRAM             = 'Weight::YOTTAGRAM';
    const ZENTNER               = 'Weight::ZENTNER';
    const ZEPTOGRAM             = 'Weight::ZEPTOGRAM';
    const ZETTAGRAM             = 'Weight::ZETTAGRAM';

    private static $_UNITS = array(
        'Weight::ARRATEL'               => array(0.5,            'arratel'),
        'Weight::ARTEL'                 => array(0.5,            'artel'),
        'Weight::ARROBA_PORTUGUESE'     => array(14.69,          'arroba'),
        'Weight::ARROBA'                => array(11.502,         '@'),
        'Weight::AS_'                   => array(0.000052,       'as'),
        'Weight::ASS'                   => array(0.000052,       'ass'),
        'Weight::ATOMIC_MASS_UNIT_1960' => array(1.6603145e-27,  'amu'),
        'Weight::ATOMIC_MASS_UNIT_1973' => array(1.6605655e-27,  'amu'),
        'Weight::ATOMIC_MASS_UNIT_1986' => array(1.6605402e-27,  'amu'),
        'Weight::ATOMIC_MASS_UNIT'      => array(1.66053873e-27, 'amu'),
        'Weight::AVOGRAM'               => array(1.6605402e-27,  'avogram'),
        'Weight::BAG'                   => array(42.63768278,    'bag'),
        'Weight::BAHT'                  => array(0.015,          'baht'),
        'Weight::BALE'                  => array(326.5865064,    'bl'),
        'Weight::BALE_US'               => array(217.7243376,    'bl'),
        'Weight::BISMAR_POUND'          => array(5.993,          'bismar pound'),
        'Weight::CANDY'                 => array(254,            'candy'),
        'Weight::CARAT_INTERNATIONAL'   => array(0.0002,         'ct'),
        'Weight::CARAT'                 => array(0.0002,         'ct'),
        'Weight::CARAT_UK'              => array(0.00025919564,  'ct'),
        'Weight::CARAT_US_1913'         => array(0.0002053,      'ct'),
        'Weight::CARGA'                 => array(140,            'carga'),
        'Weight::CATTI'                 => array(0.604875,       'catti'),
        'Weight::CATTI_JAPANESE'        => array(0.594,          'catti'),
        'Weight::CATTY'                 => array(0.5,            'catty'),
        'Weight::CATTY_JAPANESE'        => array(0.6,            'catty'),
        'Weight::CATTY_THAI'            => array(0.6,            'catty'),
        'Weight::CENTAL'                => array(45.359237,      'cH'),
        'Weight::CENTIGRAM'             => array(0.00001,        'cg'),
        'Weight::CENTNER'               => array(50,             'centner'),
        'Weight::CENTNER_RUSSIAN'       => array(100,            'centner'),
        'Weight::CHALDER'               => array(2692.52,        'chd'),
        'Weight::CHALDRON'              => array(2692.52,        'chd'),
        'Weight::CHIN'                  => array(0.5,            'chin'),
        'Weight::CHIN_JAPANESE'         => array(0.6,            'chin'),
        'Weight::CLOVE'                 => array(3.175,          'clove'),
        'Weight::CRITH'                 => array(0.000089885,    'crith'),
        'Weight::DALTON'                => array(1.6605402e-27,  'D'),
        'Weight::DAN'                   => array(50,             'dan'),
        'Weight::DAN_JAPANESE'          => array(60,             'dan'),
        'Weight::DECIGRAM'              => array(0.0001,         'dg'),
        'Weight::DECITONNE'             => array(100,            'dt'),
        'Weight::DEKAGRAM'              => array(0.01,           'dag'),
        'Weight::DEKATONNE'             => array(10000,          'dat'),
        'Weight::DENARO'                => array(0.0011,         'denaro'),
        'Weight::DENIER'                => array(0.001275,       'denier'),
        'Weight::DRACHME'               => array(0.0038,         'drachme'),
        'Weight::DRAM'                  => array(array('' => 0.45359237, '/' => 256), 'dr'),
        'Weight::DRAM_APOTHECARIES'     => array(0.0038879346,   'dr'),
        'Weight::DYNE'                  => array(1.0197162e-6,   'dyn'),
        'Weight::ELECTRON'              => array(9.109382e-31,   'e−'),
        'Weight::ELECTRONVOLT'          => array(1.782662e-36,   'eV'),
        'Weight::ETTO'                  => array(0.1,            'hg'),
        'Weight::EXAGRAM'               => array(1.0e+15,        'Eg'),
        'Weight::FEMTOGRAM'             => array(1.0e-18,        'fg'),
        'Weight::FIRKIN'                => array(25.40117272,    'fir'),
        'Weight::FLASK'                 => array(34.7,           'flask'),
        'Weight::FOTHER'                => array(979.7595192,    'fother'),
        'Weight::FOTMAL'                => array(32.65865064,    'fotmal'),
        'Weight::FUNT'                  => array(0.4095,         'funt'),
        'Weight::FUNTE'                 => array(0.4095,         'funte'),
        'Weight::GAMMA'                 => array(0.000000001,    'gamma'),
        'Weight::GIGAELECTRONVOLT'      => array(1.782662e-27,   'GeV'),
        'Weight::GIGAGRAM'              => array(1000000,        'Gg'),
        'Weight::GIGATONNE'             => array(1.0e+12,        'Gt'),
        'Weight::GIN'                   => array(0.6,            'gin'),
        'Weight::GIN_JAPANESE'          => array(0.594,          'gin'),
        'Weight::GRAIN'                 => array(0.00006479891,  'gr'),
        'Weight::GRAM'                  => array(0.001,          'g'),
        'Weight::GRAN'                  => array(0.00082,        'gran'),
        'Weight::GRANO'                 => array(0.00004905,     'grano'),
        'Weight::GRANI'                 => array(0.00004905,     'grani'),
        'Weight::GROS'                  => array(0.003824,       'gros'),
        'Weight::HECTOGRAM'             => array(0.1,            'hg'),
        'Weight::HUNDRETWEIGHT'         => array(50.80234544,    'cwt'),
        'Weight::HUNDRETWEIGHT_US'      => array(45.359237,      'cwt'),
        'Weight::HYL'                   => array(9.80665,        'hyl'),
        'Weight::JIN'                   => array(0.5,            'jin'),
        'Weight::JUPITER'               => array(1.899e+27,      'jupiter'),
        'Weight::KATI'                  => array(0.5,            'kati'),
        'Weight::KATI_JAPANESE'         => array(0.6,            'kati'),
        'Weight::KEEL'                  => array(21540.19446656, 'keel'),
        'Weight::KEG'                   => array(45.359237,      'keg'),
        'Weight::KILODALTON'            => array(1.6605402e-24,  'kD'),
        'Weight::KILOGRAM'              => array(1,              'kg'),
        'Weight::KILOGRAM_FORCE'        => array(1,              'kgf'),
        'Weight::KILOTON'               => array(1016046.9088,   'kt'),
        'Weight::KILOTON_US'            => array(907184.74,      'kt'),
        'Weight::KILOTONNE'             => array(1000000,        'kt'),
        'Weight::KIN'                   => array(0.6,            'kin'),
        'Weight::KIP'                   => array(453.59237,      'kip'),
        'Weight::KOYAN'                 => array(2419,           'koyan'),
        'Weight::KWAN'                  => array(3.75,           'kwan'),
        'Weight::LAST_GERMANY'          => array(2000,           'last'),
        'Weight::LAST'                  => array(1814.36948,     'last'),
        'Weight::LAST_WOOL'             => array(1981.29147216,  'last'),
        'Weight::LB'                    => array(0.45359237,     'lb'),
        'Weight::LBS'                   => array(0.45359237,     'lbs'),
        'Weight::LIANG'                 => array(0.05,           'liang'),
        'Weight::LIBRE_ITALIAN'         => array(0.339,          'lb'),
        'Weight::LIBRA_SPANISH'         => array(0.459,          'lb'),
        'Weight::LIBRA_PORTUGUESE'      => array(0.459,          'lb'),
        'Weight::LIBRA_ANCIENT'         => array(0.323,          'lb'),
        'Weight::LIBRA'                 => array(1,              'lb'),
        'Weight::LIVRE'                 => array(0.4895,         'livre'),
        'Weight::LONG_TON'              => array(1016.0469088,   't'),
        'Weight::LOT'                   => array(0.015,          'lot'),
        'Weight::MACE'                  => array(0.003778,       'mace'),
        'Weight::MAHND'                 => array(0.9253284348,   'mahnd'),
        'Weight::MARC'                  => array(0.24475,        'marc'),
        'Weight::MARCO'                 => array(0.23,           'marco'),
        'Weight::MARK'                  => array(0.2268,         'mark'),
        'Weight::MARK_GERMANY'          => array(0.2805,         'mark'),
        'Weight::MAUND'                 => array(37.3242,        'maund'),
        'Weight::MAUND_PAKISTAN'        => array(40,             'maund'),
        'Weight::MEGADALTON'            => array(1.6605402e-21,  'MD'),
        'Weight::MEGAGRAM'              => array(1000,           'Mg'),
        'Weight::MEGATONNE'             => array(1.0e+9,         'Mt'),
        'Weight::MERCANTILE_POUND'      => array(0.46655,        'lb merc'),
        'Weight::METRIC_TON'            => array(1000,           't'),
        'Weight::MIC'                   => array(1.0e-9,         'mic'),
        'Weight::MICROGRAM'             => array(1.0e-9,         '�g'),
        'Weight::MILLIDALTON'           => array(1.6605402e-30,  'mD'),
        'Weight::MILLIER'               => array(1000,           'millier'),
        'Weight::MILLIGRAM'             => array(0.000001,       'mg'),
        'Weight::MILLIMASS_UNIT'        => array(1.6605402e-30,  'mmu'),
        'Weight::MINA'                  => array(0.499,          'mina'),
        'Weight::MOMME'                 => array(0.00375,        'momme'),
        'Weight::MYRIAGRAM'             => array(10,             'myg'),
        'Weight::NANOGRAM'              => array(1.0e-12,        'ng'),
        'Weight::NEWTON'                => array(0.101971621,    'N'),
        'Weight::OBOL'                  => array(0.0001,         'obol'),
        'Weight::OBOLOS'                => array(0.0001,         'obolos'),
        'Weight::OBOLUS'                => array(0.0001,         'obolus'),
        'Weight::OBOLOS_ANCIENT'        => array(0.0005,         'obolos'),
        'Weight::OBOLUS_ANCIENT'        => array(0.00057,        'obolos'),
        'Weight::OKA'                   => array(1.28,           'oka'),
        'Weight::ONCA'                  => array(0.02869,        'onca'),
        'Weight::ONCE'                  => array(0.03059,        'once'),
        'Weight::ONCIA'                 => array(0.0273,         'oncia'),
        'Weight::ONZA'                  => array(0.02869,        'onza'),
        'Weight::ONS'                   => array(0.1,            'ons'),
        'Weight::OUNCE'                 => array(array('' => 0.45359237, '/' => 16),    'oz'),
        'Weight::OUNCE_FORCE'           => array(array('' => 0.45359237, '/' => 16),    'ozf'),
        'Weight::OUNCE_TROY'            => array(array('' => 65.31730128, '/' => 2100), 'oz'),
        'Weight::PACKEN'                => array(490.79,         'packen'),
        'Weight::PENNYWEIGHT'           => array(array('' => 65.31730128, '/' => 42000), 'dwt'),
        'Weight::PETAGRAM'              => array(1.0e+12,        'Pg'),
        'Weight::PFUND'                 => array(0.5,            'pfd'),
        'Weight::PICOGRAM'              => array(1.0e-15,        'pg'),
        'Weight::POINT'                 => array(0.000002,       'pt'),
        'Weight::POND'                  => array(0.5,            'pond'),
        'Weight::POUND'                 => array(0.45359237,     'lb'),
        'Weight::POUND_FORCE'           => array(0.4535237,      'lbf'),
        'Weight::POUND_METRIC'          => array(0.5,            'lb'),
        'Weight::POUND_TROY'            => array(array('' => 65.31730128, '/' => 175), 'lb'),
        'Weight::PUD'                   => array(16.3,           'pud'),
        'Weight::POOD'                  => array(16.3,           'pood'),
        'Weight::PUND'                  => array(0.5,            'pund'),
        'Weight::QIAN'                  => array(0.005,          'qian'),
        'Weight::QINTAR'                => array(50,             'qintar'),
        'Weight::QUARTER'               => array(12.70058636,    'qtr'),
        'Weight::QUARTER_US'            => array(11.33980925,    'qtr'),
        'Weight::QUARTER_TON'           => array(226.796185,     'qtr'),
        'Weight::QUARTERN'              => array(1.587573295,    'quartern'),
        'Weight::QUARTERN_LOAF'         => array(1.81436948,     'quartern-loaf'),
        'Weight::QUINTAL_FRENCH'        => array(48.95,          'q'),
        'Weight::QUINTAL'               => array(100,            'q'),
        'Weight::QUINTAL_PORTUGUESE'    => array(58.752,         'q'),
        'Weight::QUINTAL_SPAIN'         => array(45.9,           'q'),
        'Weight::REBAH'                 => array(0.2855,         'rebah'),
        'Weight::ROTL'                  => array(0.5,            'rotl'),
        'Weight::ROTEL'                 => array(0.5,            'rotel'),
        'Weight::ROTTLE'                => array(0.5,            'rottle'),
        'Weight::RATEL'                 => array(0.5,            'ratel'),
        'Weight::SACK'                  => array(165.10762268,   'sack'),
        'Weight::SCRUPLE'               => array(array('' => 65.31730128, '/' => 50400), 's'),
        'Weight::SEER'                  => array(0.933105,       'seer'),
        'Weight::SEER_PAKISTAN'         => array(1,              'seer'),
        'Weight::SHEKEL'                => array(0.01142,        'shekel'),
        'Weight::SHORT_TON'             => array(907.18474,      'st'),
        'Weight::SLINCH'                => array(175.126908,     'slinch'),
        'Weight::SLUG'                  => array(14.593903,      'slug'),
        'Weight::STONE'                 => array(6.35029318,     'st'),
        'Weight::TAEL'                  => array(0.03751,        'tael'),
        'Weight::TAHIL_JAPANESE'        => array(0.03751,        'tahil'),
        'Weight::TAHIL'                 => array(0.05,           'tahil'),
        'Weight::TALENT'                => array(30,             'talent'),
        'Weight::TAN'                   => array(50,             'tan'),
        'Weight::TECHNISCHE_MASS_EINHEIT' => array(9.80665,      'TME'),
        'Weight::TERAGRAM'              => array(1.0e+9,         'Tg'),
        'Weight::TETRADRACHM'           => array(0.014,          'tetradrachm'),
        'Weight::TICAL'                 => array(0.0164,         'tical'),
        'Weight::TOD'                   => array(12.70058636,    'tod'),
        'Weight::TOLA'                  => array(0.0116638125,   'tola'),
        'Weight::TOLA_PAKISTAN'         => array(0.0125,         'tola'),
        'Weight::TON_UK'                => array(1016.0469088,   't'),
        'Weight::TON'                   => array(1000,           't'),
        'Weight::TON_US'                => array(907.18474,      't'),
        'Weight::TONELADA_PORTUGUESE'   => array(793.15,         'tonelada'),
        'Weight::TONELADA'              => array(919.9,          'tonelada'),
        'Weight::TONNE'                 => array(1000,           't'),
        'Weight::TONNEAU'               => array(979,            'tonneau'),
        'Weight::TOVAR'                 => array(128.8,          'tovar'),
        'Weight::TROY_OUNCE'            => array(array('' => 65.31730128, '/' => 2100), 'troy oz'),
        'Weight::TROY_POUND'            => array(array('' => 65.31730128, '/' => 175),  'troy lb'),
        'Weight::TRUSS'                 => array(25.40117272,    'truss'),
        'Weight::UNCIA'                 => array(0.0272875,      'uncia'),
        'Weight::UNZE'                  => array(0.03125,        'unze'),
        'Weight::VAGON'                 => array(10000,          'vagon'),
        'Weight::YOCTOGRAM'             => array(1.0e-27,        'yg'),
        'Weight::YOTTAGRAM'             => array(1.0e+21,        'Yg'),
        'Weight::ZENTNER'               => array(50,             'Ztr'),
        'Weight::ZEPTOGRAM'             => array(1.0e-24,        'zg'),
        'Weight::ZETTAGRAM'             => array(1.0e+18,        'Zg')
    );

    private $_Locale;

    /**
     * Zend_Measure_Weight provides an locale aware class for
     * conversion and formatting of weight values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Weight Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function __construct($value, $type, $locale = false)
    {
        if (empty($locale)) {
            $this->_Locale = new Zend_Locale();
        } else {
            $this->_Locale = $locale;
        }

        $this->setValue($value, $type, $this->_Locale);
    }


    /**
     * Compare if the value and type is equal
     *
     * @param $object  object to compare equality
     * @return boolean
     */
    public function equals($object)
    {
        if ($object->toString() == $this->toString()) {
            return true;
        }

        return false;
    }


    /**
     * Set a new value
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Weight Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type, $locale = false)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        $value = Zend_Locale_Format::getNumber($value, $locale);
        if (empty(self::$_UNITS[$type])) {
            self::throwException('unknown type of weight:' . $type);
        }

        parent::setValue($value, $type, $locale);
        parent::setType($type);
    }


    /**
     * Set a new type, and convert the value
     *
     * @param $type  new type to set
     * @throws Zend_Measure_Exception
     */
    public function setType($type)
    {
        if (empty(self::$_UNITS[$type])) {
            self::throwException('unknown type of weight:' . $type);
        }

        // Convert to standard value
        $value = parent::getValue();
        if (is_array(self::$_UNITS[parent::getType()][0])) {
            foreach (self::$_UNITS[parent::getType()][0] as $key => $found) {
                switch ($key) {
                    case "/":
                        $value /= $found;
                        break;
                    default:
                        $value *= $found;
                        break;
                }
            }
        } else {
            $value = $value * (self::$_UNITS[parent::getType()][0]);
        }

        // Convert to expected value
        if (is_array(self::$_UNITS[$type][0])) {
            foreach (self::$_UNITS[$type][0] as $key => $found) {
                switch ($key) {
                    case "/":
                        $value *= $found;
                        break;
                    default:
                        $value /= $found;
                        break;
                }
            }
        } else {
            $value = $value / (self::$_UNITS[$type][0]);
        }

        parent::setValue($value, $type, $this->_Locale);
        parent::setType($type);
    }


    /**
     * Returns a string representation
     *
     * @return string
     */
    public function toString()
    {
        return parent::getValue() . ' ' . self::$_UNITS[parent::getType()][1];
    }


    /**
     * Returns a string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }


    /**
     * Returns the conversion list
     * 
     * @return array
     */
    public function getConversionList()
    {
        return self::$_UNITS;
    }
}
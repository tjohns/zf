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
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Implement needed classes
 */
require_once 'Zend.php';
require_once 'Zend/Measure/Abstract.php';
require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Length
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Length extends Zend_Measure_Abstract
{
    // Length definitions
    const STANDARD = 'Length::METER';

    const AGATE           = 'Length::AGATE';
    const ALEN_DANISH     = 'Length::ALEN_DANISH';
    const ALEN            = 'Length::ALEN';
    const ALEN_SWEDISH    = 'Length::ALEN_SWEDISH';
    const ANGSTROM        = 'Length::ANGSTROM';
    const ARMS            = 'Length::ARMS';
    const ARPENT_CANADIAN = 'Length::ARPENT_CANADIAN';
    const ARPENT          = 'Length::ARPENT';
    const ARSHEEN         = 'Length::ARSHEEN';
    const ARSHIN          = 'Length::ARSHIN';
    const ARSHIN_IRAQ     = 'Length::ARSHIN_IRAQ';
    const ASTRONOMICAL_UNIT = 'Length::ASTRONOMICAL_UNIT';
    const ATTOMETER       = 'Length::ATTOMETER';
    const BAMBOO          = 'Length::BAMBOO';
    const BARLEYCORN      = 'Length::BARLEYCORN';
    const BEE_SPACE       = 'Length::BEE_SPACE';
    const BICRON          = 'Length::BICRON';
    const BLOCK_US_EAST   = 'Length::BLOCK_US_EAST';
    const BLOCK_US_WEST   = 'Length::BLOCK_US_WEST';
    const BLOCK_US_SOUTH  = 'Length::BLOCK_US_SOUTH';
    const BOHR            = 'Length::BOHR';
    const BRACCIO         = 'Length::BRACCIO';
    const BRAZA_ARGENTINA = 'Length::BRAZA_ARGENTINA';
    const BRAZA           = 'Length::BRAZA';
    const BRAZA_US        = 'Length::BRAZA_US';
    const BUTTON          = 'Length::BUTTON';
    const CABLE_US        = 'Length::CABLE_US';
    const CABLE_UK        = 'Length::CABLE_UK';
    const CALIBER         = 'Length::CALIBER';
    const CANA            = 'Length::CANA';
    const CAPE_FOOT       = 'Length::CAPE_FOOT';
    const CAPE_INCH       = 'Length::CAPE_INCH';
    const CAPE_ROOD       = 'Length::CAPE_ROOD';
    const CENTIMETER      = 'Length::CENTIMETER';
    const CHAIN           = 'Length::CHAIN';
    const CHAIN_ENGINEER  = 'Length::CHAIN_ENGINEER';
    const CHIH            = 'Length::CHIH';
    const CHINESE_FOOT    = 'Length::CHINESE_FOOT';
    const CHINESE_INCH    = 'Length::CHINESE_INCH';
    const CHINESE_MILE    = 'Length::CHINESE_MILE';
    const CHINESE_YARD    = 'Length::CHINESE_YARD';
    const CITY_BLOCK_US_EAST  = 'Length::CITY_BLOCK_US_EAST';
    const CITY_BLOCK_US_WEST  = 'Length::CITY_BLOCK_US_WEST';
    const CITY_BLOCK_US_SOUTH = 'Length::CITY_BLOCK_US_SOUTH';
    const CLICK           = 'Length::CLICK';
    const CUADRA          = 'Length::CUADRA';
    const CUADRA_ARGENTINA= 'Length::CUADRA_ARGENTINA';
    const CUBIT_EGYPT     = 'Length:CUBIT_EGYPT';
    const CUBIT_ROYAL     = 'Length::CUBIT_ROYAL';
    const CUBIT_UK        = 'Length::CUBIT_UK';
    const CUBIT           = 'Length::CUBIT';
    const CUERDA          = 'Length::CUERDA';
    const DECIMETER       = 'Length::DECIMETER';
    const DEKAMETER       = 'Length::DEKAMETER';
    const DIDOT_POINT     = 'Length::DIDOT_POINT';
    const DIGIT           = 'Length::DIGIT';
    const DIRAA           = 'Length::DIRAA';
    const DONG            = 'Length::DONG';
    const DOUZIEME_WATCH  = 'Length::DOUZIEME_WATCH';
    const DOUZIEME        = 'Length::DOUZIEME';
    const DRA_IRAQ        = 'Length::DRA_IRAQ';
    const DRA             = 'Length::DRA';
    const EL              = 'Length::EL';
    const ELL             = 'Length::ELL';
    const ELL_SCOTTISH    = 'Length::ELL_SCOTTISH';
    const ELLE            = 'Length::ELLE';
    const ELLE_VIENNA     = 'Length::ELLE_VIENNA';
    const EM              = 'Length::EM';
    const ESTADIO_PORTUGAL= 'Length::ESTADIO_PORTUGAL';
    const ESTADIO         = 'Length::ESTADIO';
    const EXAMETER        = 'Length::EXAMETER';
    const FADEN_AUSTRIA   = 'Length::FADEN_AUSTRIA';
    const FADEN           = 'Length::FADEN';
    const FALL            = 'Length::FALL';
    const FALL_SCOTTISH   = 'Length::FALL_SCOTTISH';
    const FATHOM          = 'Length::FATHOM';
    const FATHOM_ANCIENT  = 'Length::FATHOM_ANCIENT';
    const FAUST           = 'Length::FAUST';
    const FEET_OLD_CANADIAN = 'Length::FEET_OLD_CANADIAN';
    const FEET_EGYPT      = 'Length::FEET_EGYPT';
    const FEET_FRANCE     = 'Length::FEET_FRANCE';
    const FEET            = 'Length::FEET';
    const FEET_IRAQ       = 'Length::FEET_IRAQ';
    const FEET_NETHERLAND = 'Length::FEET_NETHERLAND';
    const FEET_ITALIC     = 'Length::FEET_ITALIC';
    const FEET_SURVEY     = 'Length::FEET_SURVEY';
    const FEMTOMETER      = 'Length::FEMTOMETER';
    const FERMI           = 'Length::FERMI';
    const FINGER          = 'Length::FINGER';
    const FINGERBREADTH   = 'Length::FINGERBREADTH';
    const FIST            = 'Length::FIST';
    const FOD             = 'Length::FOD';
    const FOOT_EGYPT      = 'Length::FOOT_EGYPT';
    const FOOT_FRANCE     = 'Length::FOOT_FRANCE';
    const FOOT            = 'Length::FOOT';
    const FOOT_IRAQ       = 'Length::FOOT_IRAQ';
    const FOOT_NETHERLAND = 'Length::FOOT_NETHERLAND';
    const FOOT_ITALIC     = 'Length::FOOT_ITALIC';
    const FOOT_SURVEY     = 'Length::FOOT_SURVEY';
    const FOOTBALL_FIELD_CANADA = 'Length::FOOTBALL_FIELD_CANADA';
    const FOOTBALL_FIELD_US     = 'Length::FOOTBALL_FIELD_US';
    const FOOTBALL_FIELD  = 'Length::FOOTBALL_FIELD';
    const FURLONG         = 'Length::FURLONG';
    const FURLONG_SURVEY  = 'Length::FURLONG_SURVEY';
    const FUSS            = 'Length::FUSS';
    const GIGAMETER       = 'Length::GIGAMETER';
    const GIGAPARSEC      = 'Length::GIGAPARSEC';
    const GNATS_EYE       = 'Length::GNATS_EYE';
    const GOAD            = 'Length::GOAD';
    const GRY             = 'Length::GRY';
    const HAIRS_BREADTH   = 'Length::HAIRS_BREADTH';
    const HAND            = 'Length::HAND';
    const HANDBREADTH     = 'Length::HANDBREADTH';
    const HAT             = 'Length::HAT';
    const HECTOMETER      = 'Length::HECTOMETER';
    const HEER            = 'Length::HEER';
    const HIRO            = 'Length::HIRO';
    const HUBBLE          = 'Length::HUBBLE';
    const HVAT            = 'Length::HVAT';
    const INCH            = 'Length::INCH';
    const IRON            = 'Length::IRON';
    const KEN             = 'Length::KEN';
    const KERAT           = 'Length::KERAT';
    const KILOFOOT        = 'Length::KILOFOOT';
    const KILOMETER       = 'Length::KILOMETER';
    const KILOPARSEC      = 'Length::KILOPARSEC';
    const KILOYARD        = 'Length::KILOYARD';
    const KIND            = 'Length::KIND';
    const KLAFTER         = 'Length::KLAFTER';
    const KLAFTER_SWISS   = 'Length::KLAFTER_SWISS';
    const KLICK           = 'Length::KLICK';
    const KYU             = 'Length::KYU';
    const LAP_ANCIENT     = 'Length::LAP_ANCIENT';
    const LAP             = 'Length::LAP';
    const LAP_POOL        = 'Length::LAP_POOL';
    const LEAGUE_ANCIENT  = 'Length::LEAGUE_ANCIENT';
    const LEAGUE_NAUTIC   = 'Length::LEAGUE_NAUTIC';
    const LEAGUE_UK_NAUTIC= 'Length::LEAGUE_UK_NAUTIC';
    const LEAGUE          = 'Length::LEAGUE';
    const LEAGUE_US       = 'Length::LEAGUE_US';
    const LEAP            = 'Length::LEAP';
    const LEGOA           = 'Length::LEGOA';
    const LEGUA           = 'Length::LEGUA';
    const LEGUA_US        = 'Length::LEGUA_US';
    const LEGUA_SPAIN_OLD = 'Length::LEGUA_SPAIN_OLD';
    const LEGUA_SPAIN     = 'Length::LEGUA_SPAIN';
    const LI_ANCIENT      = 'Length::LI_ANCIENT';
    const LI_IMPERIAL     = 'Length::LI_IMPERIAL';
    const LI              = 'Length::LI';
    const LIEUE           = 'Length::LIEUE';
    const LIEUE_METRIC    = 'Length::LIEUE_METRIC';
    const LIEUE_NAUTIC    = 'Length::LIEUE_NAUTIC';
    const LIGHT_SECOND    = 'Length::LIGHT_SECOND';
    const LIGHT_MINUTE    = 'Length::LIGHT_MINUTE';
    const LIGHT_HOUR      = 'Length::LIGHT_HOUR';
    const LIGHT_DAY       = 'Length::LIGHT_DAY';
    const LIGHT_YEAR      = 'Length::LIGHT_YEAR';
    const LIGNE           = 'Length::LIGNE';
    const LIGNE_SWISS     = 'Length::LIGNE_SWISS';
    const LINE            = 'Length::LINE';
    const LINE_SMALL      = 'Length::LINE_SMALL';
    const LINK            = 'Length::LINK';
    const LINK_ENGINEER   = 'Length::LINK_ENGINEER';
    const LUG             = 'Length::LUG';
    const LUG_GREAT       = 'Length::LUG_GREAT';
    const MARATHON        = 'Length::MARATHON';
    const MARK_TWAIN      = 'Length::MARK_TWAIN';
    const MEGAMETER       = 'Length::MEGAMETER';
    const MEGAPARSEC      = 'Length::MEGAPARSEC';
    const MEILE_AUSTRIAN  = 'Length::MEILE_AUSTRIAN';
    const MEILE           = 'Length::MEILE';
    const MEILE_GERMAN    = 'Length::MEILE_GERMAN';
    const METER           = 'Length::METER';
    const METRE           = 'Length::METRE';
    const METRIC_MILE     = 'Length::METRIC_MILE';
    const METRIC_MILE_US  = 'Length::METRIC_MILE_US';
    const MICROINCH       = 'Length::MICROINCH';
    const MICROMETER      = 'Length::MICROMETER';
    const MICROMICRON     = 'Length::MICROMICRON';
    const MICRON          = 'Length::MICRON';
    const MIGLIO          = 'Length::MIGLIO';
    const MIIL            = 'Length::MIIL';
    const MIIL_DENMARK    = 'Length::MIIL_DENMARK';
    const MIIL_SWEDISH    = 'Length::MIIL_SWEDISH';
    const MIL             = 'Length::MIL';
    const MIL_SWEDISH     = 'Length::MIL_SWEDISH';
    const MILE_UK         = 'Length::MILE_UK';
    const MILE_IRISH      = 'Length::MILE_IRISH';
    const MILE            = 'Length::MILE';
    const MILE_NAUTIC     = 'Length::MILE_NAUTIC';
    const MILE_NAUTIC_UK  = 'Length::MILE_NAUTIC_UK';
    const MILE_NAUTIC_US  = 'Length::MILE_NAUTIC_US';
    const MILE_ANCIENT    = 'Length::MILE_ANCIENT';
    const MILE_SCOTTISH   = 'Length::MILE_SCOTTISH';
    const MILE_STATUTE    = 'Length::MILE_STATUTE';
    const MILE_US         = 'Length::MILE_US';
    const MILHA           = 'Length::MILHA';
    const MILITARY_PACE   = 'Length::MILITARY_PACE';
    const MILITARY_PACE_DOUBLE = 'Length::MILITARY_PACE_DOUBLE';
    const MILLA           = 'Length::MILLA';
    const MILLE           = 'Length::MILLE';
    const MILLIARE        = 'Length::MILLIARE';
    const MILLIMETER      = 'Length::MILLIMETER';
    const MILLIMICRON     = 'Length::MILLIMICRON';
    const MKONO           = 'Length::MKONO';
    const MOOT            = 'Length::MOOT';
    const MYRIAMETER      = 'Length::MYRIAMETER';
    const NAIL            = 'Length::NAIL';
    const NANOMETER       = 'Length::NANOMETER';
    const NANON           = 'Length::NANON';
    const PACE            = 'Length::PACE';
    const PACE_ROMAN      = 'Length::PACE_ROMAN';
    const PALM_DUTCH      = 'Length::PALM_DUTCH';
    const PALM_UK         = 'Length::PALM_UK';
    const PALM            = 'Length::PALM';
    const PALMO_PORTUGUESE= 'Length::PALMO_PORTUGUESE';
    const PALMO           = 'Length::PALMO';
    const PALMO_US        = 'Length::PALMO_US';
    const PARASANG        = 'Length::PARASANG';
    const PARIS_FOOT      = 'Length::PARIS_FOOT';
    const PARSEC          = 'Length::PARSEC';
    const PE              = 'Length::PE';
    const PEARL           = 'Length::PEARL';
    const PERCH           = 'Length::PERCH';
    const PERCH_IRELAND   = 'Length::PERCH_IRELAND';
    const PERTICA         = 'Length::PERTICA';
    const PES             = 'Length::PES';
    const PETAMETER       = 'Length::PETAMETER';
    const PICA            = 'Length::PICA';
    const PICOMETER       = 'Length::PICOMETER';
    const PIE_ARGENTINA   = 'Length::PIE_ARGENTINA';
    const PIE_ITALIC      = 'Length::PIE_ITALIC';
    const PIE             = 'Length::PIE';
    const PIE_US          = 'Length::PIE_US';
    const PIED_DE_ROI     = 'Length::PIED_DE_ROI';
    const PIK             = 'Length::PIK';
    const PIKE            = 'Length::PIKE';
    const POINT_ADOBE     = 'Length::POINT_ADOBE';
    const POINT           = 'Length::POINT';
    const POINT_DIDOT     = 'Length::POINT_DIDOT';
    const POINT_TEX       = 'Length::POINT_TEX';
    const POLE            = 'Length::POLE';
    const POLEGADA        = 'Length::POLEGADA';
    const POUCE           = 'Length::POUCE';
    const PU              = 'Length::PU';
    const PULGADA         = 'Length::PULGADA';
    const PYGME           = 'Length::PYGME';
    const Q               = 'Length::Q';
    const QUADRANT        = 'Length::QUADRANT';
    const QUARTER         = 'Length::QUARTER';
    const QUARTER_CLOTH   = 'Length::QUARTER_CLOTH';
    const QUARTER_PRINT   = 'Length::QUARTER_PRINT';
    const RANGE           = 'Length::RANGE';
    const REED            = 'Length::REED';
    const RI              = 'Length::RI';
    const RIDGE           = 'Length::RIDGE';
    const RIVER           = 'Length::RIVER';
    const ROD             = 'Length::ROD';
    const ROD_SURVEY      = 'Length::ROD_SURVEY';
    const ROEDE           = 'Length::ROEDE';
    const ROOD            = 'Length::ROOD';
    const ROPE            = 'Length::ROPE';
    const ROYAL_FOOT      = 'Length::ROYAL_FOOT';
    const RUTE            = 'Length::RUTE';
    const SADZHEN         = 'Length::SADZHEN';
    const SAGENE          = 'Length::SAGENE';
    const SCOTS_FOOT      = 'Length::SCOTS_FOOT';
    const SCOTS_MILE      = 'Length::SCOTS_MILE';
    const SEEMEILE        = 'Length::SEEMEILE';
    const SHACKLE         = 'Length::SHACKLE';
    const SHAFTMENT       = 'Length::SHAFTMENT';
    const SHAFTMENT_ANCIENT = 'Length::SHAFTMENT_ANCIENT';
    const SHAKU           = 'Length::SHAKU';
    const SIRIOMETER      = 'Length::SIRIOMETER';
    const SMOOT           = 'Length::SMOOT';
    const SPAN            = 'Length::SPAN';
    const SPAT            = 'Length::SPAT';
    const STADIUM         = 'Length::STADIUM';
    const STEP            = 'Length::STEP';
    const STICK           = 'Length::STICK';
    const STORY           = 'Length::STORY';
    const STRIDE          = 'Length::STRIDE';
    const STRIDE_ROMAN    = 'Length::STRIDE_ROMAN';
    const TENTHMETER      = 'Length::TENTHMETER';
    const TERAMETER       = 'Length::TERAMETER';
    const THOU            = 'Length::THOU';
    const TOISE           = 'Length::TOISE';
    const TOWNSHIP        = 'Length::TOWNSHIP';
    const T_SUN           = 'Length::T_SUN';
    const TU              = 'Length::TU';
    const TWAIN           = 'Length::TWAIN';
    const TWIP            = 'Length::TWIP';
    const U               = 'Length::U';
    const VARA_CALIFORNIA = 'Length::VARA_CALIFORNIA';
    const VARA_MEXICAN    = 'Length::VARA_MEXICAN';
    const VARA_PORTUGUESE = 'Length::VARA_PORTUGUESE';
    const VARA_AMERICA    = 'Length::VARA_AMERICA';
    const VARA            = 'Length::VARA';
    const VARA_TEXAS      = 'Length::VARA_TEXAS';
    const VERGE           = 'Length::VERGE';
    const VERSHOK         = 'Length::VERSHOK';
    const VERST           = 'Length::VERST';
    const WAH             = 'Length::WAH';
    const WERST           = 'Length::WERST';
    const X_UNIT          = 'Length::X_UNIT';
    const YARD            = 'Length::YARD';
    const YOCTOMETER      = 'Length::YOCTOMETER';
    const YOTTAMETER      = 'Length::YOTTAMETER';
    const ZEPTOMETER      = 'Length::ZEPTOMETER';
    const ZETTAMETER      = 'Length::ZETTAMETER';
    const ZOLL            = 'Length::ZOLL';
    const ZOLL_SWISS      = 'Length::ZOLL_SWISS';

    private static $_UNITS = array(
        'Length::AGATE'           => array(array('' => 0.0254, '/' => 72), 'agate'),
        'Length::ALEN_DANISH'     => array(0.6277,           'alen'),
        'Length::ALEN'            => array(0.6,              'alen'),
        'Length::ALEN_SWEDISH'    => array(0.5938,           'alen'),
        'Length::ANGSTROM'        => array(1.0e-10,          'Å'),
        'Length::ARMS'            => array(0.7,              'arms'),
        'Length::ARPENT_CANADIAN' => array(58.47,            'arpent'),
        'Length::ARPENT'          => array(58.471308,        'arpent'),
        'Length::ARSHEEN'         => array(0.7112,           'arsheen'),
        'Length::ARSHIN'          => array(1.04,             'arshin'),
        'Length::ARSHIN_IRAQ'     => array(74.5,             'arshin'),
        'Length::ASTRONOMICAL_UNIT' => array(149597870691,   'AU'),
        'Length::ATTOMETER'       => array(1.0e-18,          'am'),
        'Length::BAMBOO'          => array(3.2,              'bamboo'),
        'Length::BARLEYCORN'      => array(0.0085,           'barleycorn'),
        'Length::BEE_SPACE'       => array(0.0065,           'bee space'),
        'Length::BICRON'          => array(1.0e-12,          '��'),
        'Length::BLOCK_US_EAST'   => array(80.4672,          'block'),
        'Length::BLOCK_US_WEST'   => array(100.584,          'block'),
        'Length::BLOCK_US_SOUTH'  => array(160.9344,         'block'),
        'Length::BOHR'            => array(52.918e-12,       'a�'),
        'Length::BRACCIO'         => array(0.7,              'braccio'),
        'Length::BRAZA_ARGENTINA' => array(1.733,            'braza'),
        'Length::BRAZA'           => array(1.67,             'braza'),
        'Length::BRAZA_US'        => array(1.693,            'braza'),
        'Length::BUTTON'          => array(0.000635,         'button'),
        'Length::CABLE_US'        => array(219.456,          'cable'),
        'Length::CABLE_UK'        => array(185.3184,         'cable'),
        'Length::CALIBER'         => array(0.0254,           'cal'),
        'Length::CANA'            => array(2,                'cana'),
        'Length::CAPE_FOOT'       => array(0.314858,         'cf'),
        'Length::CAPE_INCH'       => array(array('' => 0.314858,'/' => 12), 'ci'),
        'Length::CAPE_ROOD'       => array(3.778296,         'cr'),
        'Length::CENTIMETER'      => array(0.01,             'cm'),
        'Length::CHAIN'           => array(array('' => 79200,'/' => 3937),  'ch'),
        'Length::CHAIN_ENGINEER'  => array(30.48,            'ch'),
        'Length::CHIH'            => array(0.35814,          "ch'ih"),
        'Length::CHINESE_FOOT'    => array(0.371475,         'ft'),
        'Length::CHINESE_INCH'    => array(0.0371475,        'in'),
        'Length::CHINESE_MILE'    => array(557.21,           'mi'),
        'Length::CHINESE_YARD'    => array(0.89154,          'yd'),
        'Length::CITY_BLOCK_US_EAST'  => array(80.4672,      'block'),
        'Length::CITY_BLOCK_US_WEST'  => array(100.584,      'block'),
        'Length::CITY_BLOCK_US_SOUTH' => array(160.9344,     'block'),
        'Length::CLICK'           => array(1000,             'click'),
        'Length::CUADRA'          => array(84,               'cuadra'),
        'Length::CUADRA_ARGENTINA'=> array(130,              'cuadra'),
        'Length:CUBIT_EGYPT'      => array(0.45,             'cubit'),
        'Length::CUBIT_ROYAL'     => array(0.5235,           'cubit'),
        'Length::CUBIT_UK'        => array(0.4572,           'cubit'),
        'Length::CUBIT'           => array(0.444,            'cubit'),
        'Length::CUERDA'          => array(21,               'cda'),
        'Length::DECIMETER'       => array(0.1,              'dm'),
        'Length::DEKAMETER'       => array(10,               'dam'),
        'Length::DIDOT_POINT'     => array(0.000377,         'didot point'),
        'Length::DIGIT'           => array(0.019,            'digit'),
        'Length::DIRAA'           => array(0.58,             ''),
        'Length::DONG'            => array(array('' => 7,'/' => 300), 'dong'),
        'Length::DOUZIEME_WATCH'  => array(0.000188,         'douzi�me'),
        'Length::DOUZIEME'        => array(0.00017638888889, 'douzi�me'),
        'Length::DRA_IRAQ'        => array(0.745,            'dra'),
        'Length::DRA'             => array(0.7112,           'dra'),
        'Length::EL'              => array(0.69,             'el'),
        'Length::ELL'             => array(1.143,            'ell'),
        'Length::ELL_SCOTTISH'    => array(0.945,            'ell'),
        'Length::ELLE'            => array(0.6,              'ellen'),
        'Length::ELLE_VIENNA'     => array(0.7793,           'ellen'),
        'Length::EM'              => array(0.0042175176,     'em'),
        'Length::ESTADIO_PORTUGAL'=> array(261,              'estadio'),
        'Length::ESTADIO'         => array(174,              'estadio'),
        'Length::EXAMETER'        => array(1.0e+18,          'Em'),
        'Length::FADEN_AUSTRIA'   => array(1.8965,           'faden'),
        'Length::FADEN'           => array(1.8,              'faden'),
        'Length::FALL'            => array(6.858,            'fall'),
        'Length::FALL_SCOTTISH'   => array(5.67,             'fall'),
        'Length::FATHOM'          => array(1.8288,           'fth'),
        'Length::FATHOM_ANCIENT'  => array(1.829,            'fth'),
        'Length::FAUST'           => array(0.10536,          'faust'),
        'Length::FEET_OLD_CANADIAN' => array(0.325,          'ft'),
        'Length::FEET_EGYPT'      => array(0.36,             'ft'),
        'Length::FEET_FRANCE'     => array(0.3248406,        'ft'),
        'Length::FEET'            => array(0.3048,           'ft'),
        'Length::FEET_IRAQ'       => array(0.316,            'ft'),
        'Length::FEET_NETHERLAND' => array(0.28313,          'ft'),
        'Length::FEET_ITALIC'     => array(0.296,            'ft'),
        'Length::FEET_SURVEY'     => array(array('' => 1200, '/' => 3937), 'ft'),
        'Length::FEMTOMETER'      => array(1.0e-15,          'fm'),
        'Length::FERMI'           => array(1.0e-15,          'f'),
        'Length::FINGER'          => array(0.1143,           'finger'),
        'Length::FINGERBREADTH'   => array(0.01905,          'fingerbreadth'),
        'Length::FIST'            => array(0.1,              'fist'),
        'Length::FOD'             => array(0.3141,           'fod'),
        'Length::FOOT_EGYPT'      => array(0.36,             'ft'),
        'Length::FOOT_FRANCE'     => array(0.3248406,        'ft'),
        'Length::FOOT'            => array(0.3048,           'ft'),
        'Length::FOOT_IRAQ'       => array(0.316,            'ft'),
        'Length::FOOT_NETHERLAND' => array(0.28313,          'ft'),
        'Length::FOOT_ITALIC'     => array(0.296,            'ft'),
        'Length::FOOT_SURVEY'     => array(array('' => 1200, '/' => 3937), 'ft'),
        'Length::FOOTBALL_FIELD_CANADA' => array(100.584,    'football field'),
        'Length::FOOTBALL_FIELD_US'     => array(91.44,      'football field'),
        'Length::FOOTBALL_FIELD'  => array(109.728,          'football field'),
        'Length::FURLONG'         => array(201.168,          'fur'),
        'Length::FURLONG_SURVEY'  => array(array('' => 792000, '/' => 3937), 'fur'),
        'Length::FUSS'            => array(0.31608,          'fuss'),
        'Length::GIGAMETER'       => array(1.0e+9,           'Gm'),
        'Length::GIGAPARSEC'      => array(30.85678e+24,     'Gpc'),
        'Length::GNATS_EYE'       => array(0.000125,         "gnat's eye"),
        'Length::GOAD'            => array(1.3716,           'goad'),
        'Length::GRY'             => array(0.000211667,      'gry'),
        'Length::HAIRS_BREADTH'   => array(0.0001,           "hair's breadth"),
        'Length::HAND'            => array(0.1016,           'hand'),
        'Length::HANDBREADTH'     => array(0.08,             "hand's breadth"),
        'Length::HAT'             => array(0.5,              'hat'),
        'Length::HECTOMETER'      => array(100,              'hm'),
        'Length::HEER'            => array(73.152,           'heer'),
        'Length::HIRO'            => array(1.818,            'hiro'),
        'Length::HUBBLE'          => array(9.4605e+24,       'hubble'),
        'Length::HVAT'            => array(1.8965,           'hvat'),
        'Length::INCH'            => array(0.0254,           'in'),
        'Length::IRON'            => array(array('' => 0.0254, '/' => 48), 'iron'),
        'Length::KEN'             => array(1.818,            'ken'),
        'Length::KERAT'           => array(0.0286,           'kerat'),
        'Length::KILOFOOT'        => array(304.8,            'kft'),
        'Length::KILOMETER'       => array(0.001,            'km'),
        'Length::KILOPARSEC'      => array(3.0856776e+19,    'kpc'),
        'Length::KILOYARD'        => array(914.4,            'kyd'),
        'Length::KIND'            => array(0.5,              'kind'),
        'Length::KLAFTER'         => array(1.8965,           'klafter'),
        'Length::KLAFTER_SWISS'   => array(1.8,              'klafter'),
        'Length::KLICK'           => array(1000,             'klick'),
        'Length::KYU'             => array(0.00025,          'kyu'),
        'Length::LAP_ANCIENT'     => array(402.336,          ''),
        'Length::LAP'             => array(400,              'lap'),
        'Length::LAP_POOL'        => array(100,              'lap'),
        'Length::LEAGUE_ANCIENT'  => array(2275,             'league'),
        'Length::LEAGUE_NAUTIC'   => array(5556,             'league'),
        'Length::LEAGUE_UK_NAUTIC'=> array(5559.552,         'league'),
        'Length::LEAGUE'          => array(4828,             'league'),
        'Length::LEAGUE_US'       => array(4828.0417,        'league'),
        'Length::LEAP'            => array(2.0574,           'leap'),
        'Length::LEGOA'           => array(6174.1,           'legoa'),
        'Length::LEGUA'           => array(4200,             'legua'),
        'Length::LEGUA_US'        => array(4233.4,           'legua'),
        'Length::LEGUA_SPAIN_OLD' => array(4179.4,           'legua'),
        'Length::LEGUA_SPAIN'     => array(6680,             'legua'),
        'Length::LI_ANCIENT'      => array(500,              'li'),
        'Length::LI_IMPERIAL'     => array(644.65,           'li'),
        'Length::LI'              => array(500,              'li'),
        'Length::LIEUE'           => array(3898,             'lieue'),
        'Length::LIEUE_METRIC'    => array(4000,             'lieue'),
        'Length::LIEUE_NAUTIC'    => array(5556,             'lieue'),
        'Length::LIGHT_SECOND'    => array(299792458,        'light second'),
        'Length::LIGHT_MINUTE'    => array(17987547480,      'light minute'),
        'Length::LIGHT_HOUR'      => array(1079252848800,    'light hour'),
        'Length::LIGHT_DAY'       => array(25902068371200,   'light day'),
        'Length::LIGHT_YEAR'      => array(9460528404879000, 'ly'),
        'Length::LIGNE'           => array(0.0021167,        'ligne'),
        'Length::LIGNE_SWISS'     => array(0.002256,         'ligne'),
        'Length::LINE'            => array(0.0021167,        'li'),
        'Length::LINE_SMALL'      => array(0.000635,         'li'),
        'Length::LINK'            => array(array('' => 792,'/' => 3937), 'link'),
        'Length::LINK_ENGINEER'   => array(0.3048,           'link'),
        'Length::LUG'             => array(5.0292,           'lug'),
        'Length::LUG_GREAT'       => array(6.4008,           'lug'),
        'Length::MARATHON'        => array(42194.988,        'marathon'),
        'Length::MARK_TWAIN'      => array(3.6576074,        'mark twain'),
        'Length::MEGAMETER'       => array(1000000,          'Mm'),
        'Length::MEGAPARSEC'      => array(3.085677e+22,     'Mpc'),
        'Length::MEILE_AUSTRIAN'  => array(7586,             'meile'),
        'Length::MEILE'           => array(7412.7,           'meile'),
        'Length::MEILE_GERMAN'    => array(7532.5,           'meile'),
        'Length::METER'           => array(1,                'm'),
        'Length::METRE'           => array(1,                'm'),
        'Length::METRIC_MILE'     => array(1500,             'metric mile'),
        'Length::METRIC_MILE_US'  => array(1600,             'metric mile'),
        'Length::MICROINCH'       => array(2.54e-08,         '�in'),
        'Length::MICROMETER'      => array(0.000001,         '�m'),
        'Length::MICROMICRON'     => array(1.0e-12,          '��'),
        'Length::MICRON'          => array(0.000001,         '�'),
        'Length::MIGLIO'          => array(1488.6,           'miglio'),
        'Length::MIIL'            => array(7500,             'miil'),
        'Length::MIIL_DENMARK'    => array(7532.5,           'miil'),
        'Length::MIIL_SWEDISH'    => array(10687,            'miil'),
        'Length::MIL'             => array(0.0000254,        'mil'),
        'Length::MIL_SWEDISH'     => array(10000,            'mil'),
        'Length::MILE_UK'         => array(1609,             'mi'),
        'Length::MILE_IRISH'      => array(2048,             'mi'),
        'Length::MILE'            => array(1609.344,         'mi'),
        'Length::MILE_NAUTIC'     => array(1852,             'mi'),
        'Length::MILE_NAUTIC_UK'  => array(1853.184,         'mi'),
        'Length::MILE_NAUTIC_US'  => array(1852,             'mi'),
        'Length::MILE_ANCIENT'    => array(1520,             'mi'),
        'Length::MILE_SCOTTISH'   => array(1814,             'mi'),
        'Length::MILE_STATUTE'    => array(1609.344,         'mi'),
        'Length::MILE_US'         => array(array('' => 6336000,'/' => 3937), 'mi'),
        'Length::MILHA'           => array(2087.3,           'milha'),
        'Length::MILITARY_PACE'   => array(0.762,            'mil. pace'),
        'Length::MILITARY_PACE_DOUBLE' => array(0.9144,      'mil. pace'),
        'Length::MILLA'           => array(1392,             'milla'),
        'Length::MILLE'           => array(1949,             'mille'),
        'Length::MILLIARE'        => array(0.001478,         'milliare'),
        'Length::MILLIMETER'      => array(0.001,            'mm'),
        'Length::MILLIMICRON'     => array(1.0e-9,           'm�'),
        'Length::MKONO'           => array(0.4572,           'mkono'),
        'Length::MOOT'            => array(0.0762,           'moot'),
        'Length::MYRIAMETER'      => array(10000,            'mym'),
        'Length::NAIL'            => array(0.05715,          'nail'),
        'Length::NANOMETER'       => array(1.0e-9,           'nm'),
        'Length::NANON'           => array(1.0e-9,           'nanon'),
        'Length::PACE'            => array(1.524,            'pace'),
        'Length::PACE_ROMAN'      => array(1.48,             'pace'),
        'Length::PALM_DUTCH'      => array(0.10,             'palm'),
        'Length::PALM_UK'         => array(0.075,            'palm'),
        'Length::PALM'            => array(0.2286,           'palm'),
        'Length::PALMO_PORTUGUESE'=> array(0.22,             'palmo'),
        'Length::PALMO'           => array(0.20,             'palmo'),
        'Length::PALMO_US'        => array(0.2117,           'palmo'),
        'Length::PARASANG'        => array(6000,             'parasang'),
        'Length::PARIS_FOOT'      => array(0.3248406,        'paris foot'),
        'Length::PARSEC'          => array(3.0856776e+16,    'pc'),
        'Length::PE'              => array(0.33324,          'p�'),
        'Length::PEARL'           => array(0.001757299,      'pearl'),
        'Length::PERCH'           => array(5.0292,           'perch'),
        'Length::PERCH_IRELAND'   => array(6.4008,           'perch'),
        'Length::PERTICA'         => array(2.96,             'pertica'),
        'Length::PES'             => array(0.2967,           'pes'),
        'Length::PETAMETER'       => array(1.0e+15,          'Pm'),
        'Length::PICA'            => array(0.0042175176,     'pi'),
        'Length::PICOMETER'       => array(1.0e-12,          'pm'),
        'Length::PIE_ARGENTINA'   => array(0.2889,           'pie'),
        'Length::PIE_ITALIC'      => array(0.298,            'pie'),
        'Length::PIE'             => array(0.2786,           'pie'),
        'Length::PIE_US'          => array(0.2822,           'pie'),
        'Length::PIED_DE_ROI'     => array(0.3248406,        'pied de roi'),
        'Length::PIK'             => array(0.71,             'pik'),
        'Length::PIKE'            => array(0.71,             'pike'),
        'Length::POINT_ADOBE'     => array(array('' => 0.3048, '/' => 864), 'pt'),
        'Length::POINT'           => array(0.00035,          'pt'),
        'Length::POINT_DIDOT'     => array(0.000377,         'pt'),
        'Length::POINT_TEX'       => array(0.0003514598035,  'pt'),
        'Length::POLE'            => array(5.0292,           'pole'),
        'Length::POLEGADA'        => array(0.02777,          'polegada'),
        'Length::POUCE'           => array(0.02707,          'pouce'),
        'Length::PU'              => array(1.7907,           'pu'),
        'Length::PULGADA'         => array(0.02365,          'pulgada'),
        'Length::PYGME'           => array(0.346,            'pygme'),
        'Length::Q'               => array(0.00025,          'q'),
        'Length::QUADRANT'        => array(10001300,         'quad'),
        'Length::QUARTER'         => array(402.336,          'Q'),
        'Length::QUARTER_CLOTH'   => array(0.2286,           'Q'),
        'Length::QUARTER_PRINT'   => array(0.00025,          'Q'),
        'Length::RANGE'           => array(array('' => 38016000,'/' => 3937), 'range'),
        'Length::REED'            => array(2.679,            'reed'),
        'Length::RI'              => array(3927,             'ri'),
        'Length::RIDGE'           => array(6.1722,           'ridge'),
        'Length::RIVER'           => array(2000,             'river'),
        'Length::ROD'             => array(5.0292,           'rd'),
        'Length::ROD_SURVEY'      => array(array('' => 19800, '/' => 3937), 'rd'),
        'Length::ROEDE'           => array(10,               'roede'),
        'Length::ROOD'            => array(3.7783,           'rood'),
        'Length::ROPE'            => array(3.7783,           'rope'),
        'Length::ROYAL_FOOT'      => array(0.3248406,        'royal foot'),
        'Length::RUTE'            => array(3.75,             'rute'),
        'Length::SADZHEN'         => array(2.1336,           'sadzhen'),
        'Length::SAGENE'          => array(2.1336,           'sagene'),
        'Length::SCOTS_FOOT'      => array(0.30645,          'scots foot'),
        'Length::SCOTS_MILE'      => array(1814.2,           'scots mile'),
        'Length::SEEMEILE'        => array(1852,             'seemeile'),
        'Length::SHACKLE'         => array(27.432,           'shackle'),
        'Length::SHAFTMENT'       => array(0.15124,          'shaftment'),
        'Length::SHAFTMENT_ANCIENT' => array(0.165,          'shaftment'),
        'Length::SHAKU'           => array(0.303,            'shaku'),
        'Length::SIRIOMETER'      => array(1.4959787e+17,    'siriometer'),
        'Length::SMOOT'           => array(1.7018,           'smoot'),
        'Length::SPAN'            => array(0.2286,           'span'),
        'Length::SPAT'            => array(1.0e+12,          'spat'),
        'Length::STADIUM'         => array(185,              'stadium'),
        'Length::STEP'            => array(0.762,            'step'),
        'Length::STICK'           => array(3.048,            'stk'),
        'Length::STORY'           => array(3.3,              'story'),
        'Length::STRIDE'          => array(1.524,            'stride'),
        'Length::STRIDE_ROMAN'    => array(1.48,             'stride'),
        'Length::TENTHMETER'      => array(1.0e-10,          'tenth-meter'),
        'Length::TERAMETER'       => array(1.0e+12,          'Tm'),
        'Length::THOU'            => array(0.0000254,        'thou'),
        'Length::TOISE'           => array(1.949,            'toise'),
        'Length::TOWNSHIP'        => array(array('' => 38016000,'/' => 3937), 'twp'),
        'Length::T_SUN'           => array(0.0358,           "t'sun"),
        'Length::TU'              => array(161130,           'tu'),
        'Length::TWAIN'           => array(3.6576074,        'twain'),
        'Length::TWIP'            => array(0.000017639,      'twip'),
        'Length::U'               => array(0.04445,          'U'),
        'Length::VARA_CALIFORNIA' => array(0.83820168,       'vara'),
        'Length::VARA_MEXICAN'    => array(0.83802,          'vara'),
        'Length::VARA_PORTUGUESE' => array(1.10,             'vara'),
        'Length::VARA_AMERICA'    => array(0.864,            'vara'),
        'Length::VARA'            => array(0.83587,          'vara'),
        'Length::VARA_TEXAS'      => array(0.84666836,       'vara'),
        'Length::VERGE'           => array(0.9144,           'verge'),
        'Length::VERSHOK'         => array(0.04445,          'vershok'),
        'Length::VERST'           => array(1066.8,           'verst'),
        'Length::WAH'             => array(2,                'wah'),
        'Length::WERST'           => array(1066.8,           'werst'),
        'Length::X_UNIT'          => array(1.0020722e-13,    'Xu'),
        'Length::YARD'            => array(0.9144,           'yd'),
        'Length::YOCTOMETER'      => array(1.0e-24,          'ym'),
        'Length::YOTTAMETER'      => array(1.0e+24,          'Ym'),
        'Length::ZEPTOMETER'      => array(1.0e-21,          'zm'),
        'Length::ZETTAMETER'      => array(1.0e+21,          'Zm'),
        'Length::ZOLL'            => array(0.02634,          'zoll'),
        'Length::ZOLL_SWISS'      => array(0.03,             'zoll')
    );

    private $_Locale;

    /**
     * Zend_Measure_Length provides an locale aware class for
     * conversion and formatting of length values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Length Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Length Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type, $locale = false)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        try {
            $value = Zend_Locale_Format::getNumber($value, $locale);
        } catch(Exception $e) {
            throw Zend::exception('Zend_Measure_Exception', $e->getMessage());
        }

        if (empty(self::$_UNITS[$type])) {
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of length:' . $type);
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of length:' . $type);
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
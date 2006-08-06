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
require_once 'Zend/Locale/Data.php';
require_once 'Zend/Locale/Format.php';


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

    const AGATE           = 'Length::AGATE';           // Printing
    const ALEN_DANISH     = 'Length::ALEN_DANISH';     // Danish
    const ALEN            = 'Length::ALEN';            // Scandinavian
    const ALEN_SWEDISH    = 'Length::ALEN_SWEDISH';    // Swedish
    const ANGSTROM        = 'Length::ANGSTROM';        // Atomic
    const ARMS            = 'Length::ARMS';            // UK
    const ARPENT_CANADIAN = 'Length::ARPENT_CANADIAN'; // French
    const ARPENT          = 'Length::ARPENT';          // French
    const ARSHEEN         = 'Length::ARSHEEN';         // Russian
    const ARSHIN          = 'Length::ARSHIN';          // Iran
    const ARSHIN_IRAQ     = 'Length::ARSHIN_IRAQ';     // Iraq
    const ASTRONOMICAL_UNIT = 'Length::ASTRONOMICAL_UNIT'; // Astonomical
    const ATTOMETER       = 'Length::ATTOMETER';       // Metric
    const BAMBOO          = 'Length::BAMBOO';          // unknown
    const BARLEYCORN      = 'Length::BARLEYCORN';      // UK
    const BEE_SPACE       = 'Length::BEE_SPACE';       // Natural
    const BICRON          = 'Length::BICRON';          // Metric depriciated
    const BLOCK_US_EAST   = 'Length::BLOCK_US_EAST';   // US
    const BLOCK_US_WEST   = 'Length::BLOCK_US_WEST';   // US
    const BLOCK_US_SOUTH  = 'Length::BLOCK_US_SOUTH';  // US
    const BOHR            = 'Length::BOHR';            // Atomic
    const BRACCIO         = 'Length::BRACCIO';         // Italic
    const BRAZA_ARGENTINA = 'Length::BRAZA_ARGENTINA'; // Argentina
    const BRAZA           = 'Length::BRAZA';           // Spain
    const BRAZA_US        = 'Length::BRAZA_US';        // US
    const BUTTON          = 'Length::BUTTON';          // IT
    const CABLE_US        = 'Length::CABLE_US';        // Nautic
    const CABLE_UK        = 'Length::CABLE_UK';        // Nautic
    const CALIBER         = 'Length::CALIBER';         // Technic
    const CANA            = 'Length::CANA';            // Mediterran
    const CAPE_FOOT       = 'Length::CAPE_FOOT';       // South Africa
    const CAPE_INCH       = 'Length::CAPE_INCH';       // South Africa
    const CAPE_ROOD       = 'Length::CAPE_ROOD';       // South Africa
    const CENTIMETER      = 'Length::CENTIMETER';      // Metric
    const CHAIN           = 'Length::CHAIN';           // UK
    const CHAIN_ENGINEER  = 'Length::CHAIN_ENGINEER';  // US
    const CHIH            = 'Length::CHIH';            // Chinese
    const CHINESE_FOOT    = 'Length::CHINESE_FOOT';    // Chinese
    const CHINESE_INCH    = 'Length::CHINESE_INCH';    // Chinese
    const CHINESE_MILE    = 'Length::CHINESE_MILE';    // Chinese
    const CHINESE_YARD    = 'Length::CHINESE_YARD';    // Chinese
    const CITY_BLOCK_US_EAST  = 'Length::CITY_BLOCK_US_EAST';  // US
    const CITY_BLOCK_US_WEST  = 'Length::CITY_BLOCK_US_WEST';  // US
    const CITY_BLOCK_US_SOUTH = 'Length::CITY_BLOCK_US_SOUTH'; // US
    const CLICK           = 'Length::CLICK';           // US
    const CUADRA          = 'Length::CUADRA';          // American
    const CUADRA_ARGENTINA= 'Length::CUADRA_ARGENTINA';// Argentina
    const CUBIT_EGYPT     = 'Length:CUBIT_EGYPT';      // Ancient
    const CUBIT_ROYAL     = 'Length::CUBIT_ROYAL';     // Ancient
    const CUBIT_UK        = 'Length::CUBIT_UK';        // UK
    const CUBIT           = 'Length::CUBIT';           // Ancient
    const CUERDA          = 'Length::CUERDA';          // Guatemala
    const DECIMETER       = 'Length::DECIMETER';       // Metric
    const DEKAMETER       = 'Length::DEKAMETER';       // Metric
    const DIDOT_POINT     = 'Length::DIDOT_POINT';     // Technic
    const DIGIT           = 'Length::DIGIT';           // Ancient
    const DIRAA           = 'Length::DIRAA';           // Egyptian
    const DONG            = 'Length::DONG';            // Vietnam
    const DOUZIEME_WATCH  = 'Length::DOUZIEME_WATCH';  // Technical
    const DOUZIEME        = 'Length::DOUZIEME';        // Technical
    const DRA_IRAQ        = 'Length::DRA_IRAQ';        // Iraq
    const DRA             = 'Length::DRA';             // Russian
    const EL              = 'Length::EL';              // Dutch
    const ELL             = 'Length::ELL';             // UK
    const ELL_SCOTTISH    = 'Length::ELL_SCOTTISH';    // Scottish
    const ELLE            = 'Length::ELLE';            // German
    const ELLE_VIENNA     = 'Length::ELLE_VIENNA';     // Austrian
    const EM              = 'Length::EM';              // Technical
    const ESTADIO_PORTUGAL= 'Length::ESTADIO_PORTUGAL';// Spanish
    const ESTADIO         = 'Length::ESTADIO';         // Spanish
    const EXAMETER        = 'Length::EXAMETER';        // Metric
    const FADEN_AUSTRIA   = 'Length::FADEN_AUSTRIA';   // Austrian
    const FADEN           = 'Length::FADEN';           // Switzerland
    const FALL            = 'Length::FALL';            // UK
    const FALL_SCOTTISH   = 'Length::FALL_SCOTTISH';   // Scottish
    const FATHOM          = 'Length::FATHOM';          // Nautic
    const FATHOM_ANCIENT  = 'Length::FATHOM_ANCIENT';  // Ancient
    const FAUST           = 'Length::FAUST';           // Hungarian
    const FEET_OLD_CANADIAN = 'Length::FEET_OLD_CANADIAN'; // Canadian
    const FEET_EGYPT      = 'Length::FEET_EGYPT';      // Egypt
    const FEET_FRANCE     = 'Length::FEET_FRANCE';     // French
    const FEET            = 'Length::FEET';            // US
    const FEET_IRAQ       = 'Length::FEET_IRAQ';       // Iraq
    const FEET_NETHERLAND = 'Length::FEET_NETHERLAND'; // Netherland
    const FEET_ITALIC     = 'Length::FEET_ITALIC';     // Italic
    const FEET_SURVEY     = 'Length::FEET_SURVEY';     // US
    const FEMTOMETER      = 'Length::FEMTOMETER';      // Metric
    const FERMI           = 'Length::FERMI';           // Metric
    const FINGER          = 'Length::FINGER';          // Ancient
    const FINGERBREADTH   = 'Length::FINGERBREADTH';   // Ancient
    const FIST            = 'Length::FIST';            // UK
    const FOD             = 'Length::FOD';             // Danish
    const FOOT_EGYPT      = 'Length::FOOT_EGYPT';      // Egypt
    const FOOT_FRANCE     = 'Length::FOOT_FRANCE';     // French
    const FOOT            = 'Length::FOOT';            // US
    const FOOT_IRAQ       = 'Length::FOOT_IRAQ';       // Iraq
    const FOOT_NETHERLAND = 'Length::FOOT_NETHERLAND'; // Netherland
    const FOOT_ITALIC     = 'Length::FOOT_ITALIC';     // Italic
    const FOOT_SURVEY     = 'Length::FOOT_SURVEY';     // US
    const FOOTBALL_FIELD_CANADA = 'Length::FOOTBALL_FIELD_CANADA'; // Canadian
    const FOOTBALL_FIELD_US     = 'Length::FOOTBALL_FIELD_US';     // US
    const FOOTBALL_FIELD  = 'Length::FOOTBALL_FIELD';  // US
    const FURLONG         = 'Length::FURLONG';         // UK
    const FURLONG_SURVEY  = 'Length::FURLONG_SURVEY';  // UK
    const FUSS            = 'Length::FUSS';            // German
    const GIGAMETER       = 'Length::GIGAMETER';       // Metric
    const GIGAPARSEC      = 'Length::GIGAPARSEC';      // Astronomical
    const GNATS_EYE       = 'Length::GNATS_EYE';       // Idomic
    const GOAD            = 'Length::GOAD';            // Traditional
    const GRY             = 'Length::GRY';             // UK
    const HAIRS_BREADTH   = 'Length::HAIRS_BREADTH';   // Informal
    const HAND            = 'Length::HAND';            // Traditional
    const HANDBREADTH     = 'Length::HANDBREADTH';     // Traditional
    const HAT             = 'Length::HAT';             // Cambodian
    const HECTOMETER      = 'Length::HECTOMETER';      // Metric
    const HEER            = 'Length::HEER';            // Clothing
    const HIRO            = 'Length::HIRO';            // Japanese
    const HUBBLE          = 'Length::HUBBLE';          // Astronomical
    const HVAT            = 'Length::HVAT';            // Croatian
    const INCH            = 'Length::INCH';            // US
    const IRON            = 'Length::IRON';            // Clothing
    const KEN             = 'Length::KEN';             // Japanese
    const KERAT           = 'Length::KERAT';           // Middle Eastern
    const KILOFOOT        = 'Length::KILOFOOT';        // UK
    const KILOMETER       = 'Length::KILOMETER';       // Metric
    const KILOPARSEC      = 'Length::KILOPARSEC';      // Astronomical
    const KILOYARD        = 'Length::KILOYARD';        // UK
    const KIND            = 'Length::KIND';            // Ancient
    const KLAFTER         = 'Length::KLAFTER';         // German
    const KLAFTER_SWISS   = 'Length::KLAFTER_SWISS';   // Swiss
    const KLICK           = 'Length::KLICK';           // US
    const KYU             = 'Length::KYU';             // Typographic
    const LAP_ANCIENT     = 'Length::LAP_ANCIENT';     // Olympic
    const LAP             = 'Length::LAP';             // Olympic
    const LAP_POOL        = 'Length::LAP_POOL';        // Olympic
    const LEAGUE_ANCIENT  = 'Length::LEAGUE_ANCIENT';  // Ancient
    const LEAGUE_NAUTIC   = 'Length::LEAGUE_NAUTIC';   // Ancient
    const LEAGUE_UK_NAUTIC= 'Length::LEAGUE_UK_NAUTIC';// Ancient
    const LEAGUE          = 'Length::LEAGUE';          // Ancient
    const LEAGUE_US       = 'Length::LEAGUE_US';       // Ancient
    const LEAP            = 'Length::LEAP';            // Welsh
    const LEGOA           = 'Length::LEGOA';           // Portuguese
    const LEGUA           = 'Length::LEGUA';           // Spanish
    const LEGUA_US        = 'Length::LEGUA_US';        // US
    const LEGUA_SPAIN_OLD = 'Length::LEGUA_SPAIN_OLD'; // Ancient
    const LEGUA_SPAIN     = 'Length::LEGUA_SPAIN';     // Spain
    const LI_ANCIENT      = 'Length::LI_ANCIENT';      // Chinese
    const LI_IMPERIAL     = 'Length::LI_IMPERIAL';     // Chinese
    const LI              = 'Length::LI';              // Chinese
    const LIEUE           = 'Length::LIEUE';           // French
    const LIEUE_METRIC    = 'Length::LIEUE_METRIC';    // French
    const LIEUE_NAUTIC    = 'Length::LIEUE_NAUTIC';    // French
    const LIGHT_SECOND    = 'Length::LIGHT_SECOND';    // Astronomical
    const LIGHT_MINUTE    = 'Length::LIGHT_MINUTE';    // Astronomical
    const LIGHT_HOUR      = 'Length::LIGHT_HOUR';      // Astronomical
    const LIGHT_DAY       = 'Length::LIGHT_DAY';       // Astronomical
    const LIGHT_YEAR      = 'Length::LIGHT_YEAR';      // Astronomical
    const LIGNE           = 'Length::LIGNE';           // French
    const LIGNE_SWISS     = 'Length::LIGNE_SWISS';     // Swiss
    const LINE            = 'Length::LINE';            // Technical
    const LINE_SMALL      = 'Length::LINE_SMALL';      // Technical
    const LINK            = 'Length::LINK';            // UK
    const LINK_ENGINEER   = 'Length::LINK_ENGINEER';   // US
    const LUG             = 'Length::LUG';             // UK
    const LUG_GREAT       = 'Length::LUG_GREAT';       // UK
    const MARATHON        = 'Length::MARATHON';        // Olympic
    const MARK_TWAIN      = 'Length::MARK_TWAIN';      // US
    const MEGAMETER       = 'Length::MEGAMETER';       // Metric
    const MEGAPARSEC      = 'Length::MEGAPARSEC';      // Astronomical
    const MEILE_AUSTRIAN  = 'Length::MEILE_AUSTRIAN';  // German
    const MEILE           = 'Length::MEILE';           // German
    const MEILE_GERMAN    = 'Length::MEILE_GERMAN';    // German
    const METER           = 'Length::METER';           // Metric
    const METRE           = 'Length::METRE';           // Metric
    const METRIC_MILE     = 'Length::METRIC_MILE';     // Olympic
    const METRIC_MILE_US  = 'Length::METRIC_MILE_US';  // US
    const MICROINCH       = 'Length::MICROINCH';       // UK
    const MICROMETER      = 'Length::MICROMETER';      // Metric
    const MICROMICRON     = 'Length::MICROMICRON';     // Metric
    const MICRON          = 'Length::MICRON';          // Metric
    const MIGLIO          = 'Length::MIGLIO';          // Italic
    const MIIL            = 'Length::MIIL';            // Scandinavic
    const MIIL_DENMARK    = 'Length::MIIL_DENMARK';    // Denmark
    const MIIL_SWEDISH    = 'Length::MIIL_SWEDISH';    // Swedish
    const MIL             = 'Length::MIL';             // UK
    const MIL_SWEDISH     = 'Length::MIL_SWEDISH';     // Swedish
    const MILE_UK         = 'Length::MILE_UK';         // UK
    const MILE_IRISH      = 'Length::MILE_IRISH';      // UK
    const MILE            = 'Length::MILE';            // US
    const MILE_NAUTIC     = 'Length::MILE_NAUTIC';     // Nautic
    const MILE_NAUTIC_UK  = 'Length::MILE_NAUTIC_UK';  // Nautic
    const MILE_NAUTIC_US  = 'Length::MILE_NAUTIC_US';  // Nautic
    const MILE_ANCIENT    = 'Length::MILE_ANCIENT';    // Roman
    const MILE_SCOTTISH   = 'Length::MILE_SCOTTISH';   // UK
    const MILE_STATUTE    = 'Length::MILE_STATUTE';    // UK
    const MILE_US         = 'Length::MILE_US';         // US
    const MILHA           = 'Length::MILHA';           // Portuguese
    const MILITARY_PACE   = 'Length::MILITARY_PACE';   // US
    const MILITARY_PACE_DOUBLE = 'Length::MILITARY_PACE_DOUBLE'; // US
    const MILLA           = 'Length::MILLA';           // Spanish
    const MILLE           = 'Length::MILLE';           // French
    const MILLIARE        = 'Length::MILLIARE';        // Italic
    const MILLIMETER      = 'Length::MILLIMETER';      // Metric
    const MILLIMICRON     = 'Length::MILLIMICRON';     // Metric
    const MKONO           = 'Length::MKONO';           // African
    const MOOT            = 'Length::MOOT';            // Indian
    const MYRIAMETER      = 'Length::MYRIAMETER';      // Metric
    const NAIL            = 'Length::NAIL';            // Ancient
    const NANOMETER       = 'Length::NANOMETER';       // Metric
    const NANON           = 'Length::NANON';           // Metric
    const PACE            = 'Length::PACE';            // UK
    const PACE_ROMAN      = 'Length::PACE_ROMAN';      // Ancient
    const PALM_DUTCH      = 'Length::PALM_DUTCH';      // Dutch
    const PALM_UK         = 'Length::PALM_UK';         // UK
    const PALM            = 'Length::PALM';            // US
    const PALMO_PORTUGUESE= 'Length::PALMO_PORTUGUESE';// Portuguese
    const PALMO           = 'Length::PALMO';           // Spain
    const PALMO_US        = 'Length::PALMO_US';        // US
    const PARASANG        = 'Length::PARASANG';        // Ancient
    const PARIS_FOOT      = 'Length::PARIS_FOOT';      // Frensh
    const PARSEC          = 'Length::PARSEC';          // Astronomical
    const PE              = 'Length::PE';              // Portuguese
    const PEARL           = 'Length::PEARL';           // Unknown
    const PERCH           = 'Length::PERCH';           // Norman
    const PERCH_IRELAND   = 'Length::PERCH_IRELAND';   // Ireland
    const PERTICA         = 'Length::PERTICA';         // Unknown
    const PES             = 'Length::PES';             // Roman
    const PETAMETER       = 'Length::PETAMETER';       // Metric
    const PICA            = 'Length::PICA';            // Typographic
    const PICOMETER       = 'Length::PICOMETER';       // Metric
    const PIE_ARGENTINA   = 'Length::PIE_ARGENTINA';   // Argentina
    const PIE_ITALIC      = 'Length::PIE_ITALIC';      // Italic
    const PIE             = 'Length::PIE';             // Spain
    const PIE_US          = 'Length::PIE_US';          // US
    const PIED_DE_ROI     = 'Length::PIED_DE_ROI';     // France
    const PIK             = 'Length::PIK';             // Near East
    const PIKE            = 'Length::PIKE';            // Greece
    const POINT_ADOBE     = 'Length::POINT_ADOBE';     // Technic
    const POINT           = 'Length::POINT';           // Technic
    const POINT_DIDOT     = 'Length::POINT_DIDOT';     // Technic
    const POINT_TEX       = 'Length::POINT_TEX';       // Technic
    const POLE            = 'Length::POLE';            // UK
    const POLEGADA        = 'Length::POLEGADA';        // Portuguese
    const POUCE           = 'Length::POUCE';           // French
    const PU              = 'Length::PU';              // China
    const PULGADA         = 'Length::PULGADA';         // Spain
    const PYGME           = 'Length::PYGME';           // Greece
    const Q               = 'Length::Q';               // Metric
    const QUADRANT        = 'Length::QUADRANT';        // Nautic
    const QUARTER         = 'Length::QUARTER';         // Atletic
    const QUARTER_CLOTH   = 'Length::QUARTER_CLOTH';   // Cloth
    const QUARTER_PRINT   = 'Length::QUARTER_PRINT';   // Technic
    const RANGE           = 'Length::RANGE';           // Unknown
    const REED            = 'Length::REED';            // Israel
    const RI              = 'Length::RI';              // Japanese
    const RIDGE           = 'Length::RIDGE';           // Welsh
    const RIVER           = 'Length::RIVER';           // Egypt
    const ROD             = 'Length::ROD';             // UK
    const ROD_SURVEY      = 'Length::ROD_SURVEY';      // US
    const ROEDE           = 'Length::ROEDE';           // Dutch
    const ROOD            = 'Length::ROOD';            // Ancient
    const ROPE            = 'Length::ROPE';            // Ancient
    const ROYAL_FOOT      = 'Length::ROYAL_FOOT';      // French
    const RUTE            = 'Length::RUTE';            // German
    const SADZHEN         = 'Length::SADZHEN';         // Russian
    const SAGENE          = 'Length::SAGENE';          // Unknown
    const SCOTS_FOOT      = 'Length::SCOTS_FOOT';      // Scottish
    const SCOTS_MILE      = 'Length::SCOTS_MILE';      // Scottish
    const SEEMEILE        = 'Length::SEEMEILE';        // German
    const SHACKLE         = 'Length::SHACKLE';         // Ancient
    const SHAFTMENT       = 'Length::SHAFTMENT';       // UK
    const SHAFTMENT_ANCIENT = 'Length::SHAFTMENT_ANCIENT'; // Ancient
    const SHAKU           = 'Length::SHAKU';           // Japanese
    const SIRIOMETER      = 'Length::SIRIOMETER';      // Unknown
    const SMOOT           = 'Length::SMOOT';           // Funny
    const SPAN            = 'Length::SPAN';            // UK
    const SPAT            = 'Length::SPAT';            // Astronomical
    const STADIUM         = 'Length::STADIUM';         // Greece
    const STEP            = 'Length::STEP';            // US
    const STICK           = 'Length::STICK';           // Technical
    const STORY           = 'Length::STORY';           // Technical
    const STRIDE          = 'Length::STRIDE';          // UK
    const STRIDE_ROMAN    = 'Length::STRIDE_ROMAN';    // Ancient
    const TENTHMETER      = 'Length::TENTHMETER';      // Atomic
    const TERAMETER       = 'Length::TERAMETER';       // Metric
    const THOU            = 'Length::THOU';            // UK
    const TOISE           = 'Length::TOISE';           // French
    const TOWNSHIP        = 'Length::TOWNSHIP';        // US
    const T_SUN           = 'Length::T_SUN';           // Chinese
    const TU              = 'Length::TU';              // Chinese
    const TWAIN           = 'Length::TWAIN';           // US
    const TWIP            = 'Length::TWIP';            // Technical
    const U               = 'Length::U';               // Metric
    const VARA_CALIFORNIA = 'Length::VARA_CALIFORNIA'; // US
    const VARA_MEXICAN    = 'Length::VARA_MEXICAN';    // US
    const VARA_PORTUGUESE = 'Length::VARA_PORTUGUESE'; // Portuguese
    const VARA_AMERICA    = 'Length::VARA_AMERICA';    // US
    const VARA            = 'Length::VARA';            // Spanish
    const VARA_TEXAS      = 'Length::VARA_TEXAS';      // US
    const VERGE           = 'Length::VERGE';           // French
    const VERSHOK         = 'Length::VERSHOK';         // Russian
    const VERST           = 'Length::VERST';           // Russian
    const WAH             = 'Length::WAH';             // Thai
    const WERST           = 'Length::WERST';           // German
    const X_UNIT          = 'Length::X_UNIT';          // Technical
    const YARD            = 'Length::YARD';            // UK
    const YOCTOMETER      = 'Length::YOCTOMETER';      // Metric
    const YOTTAMETER      = 'Length::YOTTAMETER';      // Metric
    const ZEPTOMETER      = 'Length::ZEPTOMETER';      // Metric
    const ZETTAMETER      = 'Length::ZETTAMETER';      // Metric
    const ZOLL            = 'Length::ZOLL';            // German
    const ZOLL_SWISS      = 'Length::ZOLL_SWISS';      // Swiss

    private static $_UNITS = array(
        'Length::AGATE'           => array(array('' => 0.0254, '/' => 72),'agate'),
        'Length::ALEN_DANISH'     => array(0.6277,'alen'),
        'Length::ALEN'            => array(0.6,'alen'),
        'Length::ALEN_SWEDISH'    => array(0.5938,'alen'),
        'Length::ANGSTROM'        => array(1.0e-10,'A'),
        // TODO: Unit of Angstrom is UniCode 'U+212B'
        'Length::ARMS'            => array(0.7,'arms'),
        'Length::ARPENT_CANADIAN' => array(58.47,'arpent'),
        'Length::ARPENT'          => array(58.471308,'arpent'),
        'Length::ARSHEEN'         => array(0.7112,'arsheen'),
        'Length::ARSHIN'          => array(1.04,'arshin'),
        'Length::ARSHIN_IRAQ'     => array(74.5,'arshin'),
        'Length::ASTRONOMICAL_UNIT' => array(149597870691,'AU'),
        'Length::ATTOMETER'       => array(1.0e-18,'am'),
        'Length::BAMBOO'          => array(3.2,'bamboo'),
        'Length::BARLEYCORN'      => array(0.0085,'barleycorn'),
        'Length::BEE_SPACE'       => array(0.0065,'bee space'),
        'Length::BICRON'          => array(1.0e-12,'µµ'),
        'Length::BLOCK_US_EAST'   => array(80.4672,'block'),
        'Length::BLOCK_US_WEST'   => array(100.584,'block'),
        'Length::BLOCK_US_SOUTH'  => array(160.9344,'block'),
        'Length::BOHR'            => array(52.918e-12,'a°'),
        'Length::BRACCIO'         => array(0.7,'braccio'),
        'Length::BRAZA_ARGENTINA' => array(1.733,'braza'),
        'Length::BRAZA'           => array(1.67,'braza'),
        'Length::BRAZA_US'        => array(1.693,'braza'),
        'Length::BUTTON'          => array(0.000635,'button'),
        'Length::CABLE_US'        => array(219.456,'cable'),
        'Length::CABLE_UK'        => array(185.3184,'cable'),
        'Length::CALIBER'         => array(0.0254,'cal'),
        'Length::CANA'            => array(2,'cana'),
        'Length::CAPE_FOOT'       => array(0.314858,'cf'),
        'Length::CAPE_INCH'       => array(array('' => 0.314858,'/' => 12),'ci'),
        'Length::CAPE_ROOD'       => array(3.778296,'cr'),
        'Length::CENTIMETER'      => array(0.01,'cm'),
        'Length::CHAIN'           => array(array('' => 79200,'/' => 3937),'ch'),
        'Length::CHAIN_ENGINEER'  => array(30.48,'ch'),
        'Length::CHIH'            => array(0.35814,"ch'ih"),
        'Length::CHINESE_FOOT'    => array(0.371475,'ft'),
        'Length::CHINESE_INCH'    => array(0.0371475,'in'),
        'Length::CHINESE_MILE'    => array(557.21,'mi'),
        'Length::CHINESE_YARD'    => array(0.89154,'yd'),
        'Length::CITY_BLOCK_US_EAST'  => array(80.4672,'block'),
        'Length::CITY_BLOCK_US_WEST'  => array(100.584,'block'),
        'Length::CITY_BLOCK_US_SOUTH' => array(160.9344,'block'),
        'Length::CLICK'           => array(1000,'click'),
        'Length::CUADRA'          => array(84,'cuadra'),
        'Length::CUADRA_ARGENTINA'=> array(130,'cuadra'),
        'Length:CUBIT_EGYPT'      => array(0.45,'cubit'),
        'Length::CUBIT_ROYAL'     => array(0.5235,'cubit'),
        'Length::CUBIT_UK'        => array(0.4572,'cubit'),
        'Length::CUBIT'           => array(0.444,'cubit'),
        'Length::CUERDA'          => array(21,'cda'),
        'Length::DECIMETER'       => array(0.1,'dm'),
        'Length::DEKAMETER'       => array(10,'dam'),
        'Length::DIDOT_POINT'     => array(0.000377,'didot point'),
        'Length::DIGIT'           => array(0.019,'digit'),
        'Length::DIRAA'           => array(0.58,''),
        'Length::DONG'            => array(array('' => 7,'/' => 300),'dong'),
        'Length::DOUZIEME_WATCH'  => array(0.000188,'douzième'),
        'Length::DOUZIEME'        => array(0.00017638888889,'douzième'),
        'Length::DRA_IRAQ'        => array(0.745,'dra'),
        'Length::DRA'             => array(0.7112,'dra'),
        'Length::EL'              => array(0.69,'el'),
        'Length::ELL'             => array(1.143,'ell'),
        'Length::ELL_SCOTTISH'    => array(0.945,'ell'),
        'Length::ELLE'            => array(0.6,'ellen'),
        'Length::ELLE_VIENNA'     => array(0.7793,'ellen'),
        'Length::EM'              => array(0.0042175176,'em'),
        'Length::ESTADIO_PORTUGAL'=> array(261,'estadio'),
        'Length::ESTADIO'         => array(174,'estadio'),
        'Length::EXAMETER'        => array(1.0e+18,'Em'),
        'Length::FADEN_AUSTRIA'   => array(1.8965,'faden'),
        'Length::FADEN'           => array(1.8,'faden'),
        'Length::FALL'            => array(6.858,'fall'),
        'Length::FALL_SCOTTISH'   => array(5.67,'fall'),
        'Length::FATHOM'          => array(1.8288,'fth'),
        'Length::FATHOM_ANCIENT'  => array(1.829,'fth'),
        'Length::FAUST'           => array(0.10536,'faust'),
        'Length::FEET_OLD_CANADIAN' => array(0.325,'ft'),
        'Length::FEET_EGYPT'      => array(0.36,'ft'),
        'Length::FEET_FRANCE'     => array(0.3248406,'ft'),
        'Length::FEET'            => array(0.3048,'ft'),
        'Length::FEET_IRAQ'       => array(0.316,'ft'),
        'Length::FEET_NETHERLAND' => array(0.28313,'ft'),
        'Length::FEET_ITALIC'     => array(0.296,'ft'),
        'Length::FEET_SURVEY'     => array(array('' => 1200, '/' => 3937),'ft'),
        'Length::FEMTOMETER'      => array(1.0e-15,'fm'),
        'Length::FERMI'           => array(1.0e-15,'f'),
        'Length::FINGER'          => array(0.1143,'finger'),
        'Length::FINGERBREADTH'   => array(0.01905,'fingerbreadth'),
        'Length::FIST'            => array(0.1,'fist'),
        'Length::FOD'             => array(0.3141,'fod'),
        'Length::FOOT_EGYPT'      => array(0.36,'ft'),
        'Length::FOOT_FRANCE'     => array(0.3248406,'ft'),
        'Length::FOOT'            => array(0.3048,'ft'),
        'Length::FOOT_IRAQ'       => array(0.316,'ft'),
        'Length::FOOT_NETHERLAND' => array(0.28313,'ft'),
        'Length::FOOT_ITALIC'     => array(0.296,'ft'),
        'Length::FOOT_SURVEY'     => array(array('' => 1200, '/' => 3937),'ft'),
        'Length::FOOTBALL_FIELD_CANADA' => array(100.584,'football field'),
        'Length::FOOTBALL_FIELD_US'     => array(91.44,'football field'),
        'Length::FOOTBALL_FIELD'  => array(109.728,'football field'),
        'Length::FURLONG'         => array(201.168,'fur'),
        'Length::FURLONG_SURVEY'  => array(array('' => 792000, '/' => 3937),'fur'),
        'Length::FUSS'            => array(0.31608,'fuss'),
        'Length::GIGAMETER'       => array(1.0e+9,'Gm'),
        'Length::GIGAPARSEC'      => array(30.85678e+24,'Gpc'),
        'Length::GNATS_EYE'       => array(0.000125,"gnat's eye"),
        'Length::GOAD'            => array(1.3716,'goad'),
        'Length::GRY'             => array(0.000211667,'gry'),
        'Length::HAIRS_BREADTH'   => array(0.0001,"hair's breadth"),
        'Length::HAND'            => array(0.1016,'hand'),
        'Length::HANDBREADTH'     => array(0.08,"hand's breadth"),
        'Length::HAT'             => array(0.5,'hat'),
        'Length::HECTOMETER'      => array(100,'hm'),
        'Length::HEER'            => array(73.152,'heer'),
        'Length::HIRO'            => array(1.818,'hiro'),
        'Length::HUBBLE'          => array(9.4605e+24,'hubble'),
        'Length::HVAT'            => array(1.8965,'hvat'),
        'Length::INCH'            => array(0.0254,'in'),
        'Length::IRON'            => array(array('' => 0.0254, '/' => 48),'iron'),
        'Length::KEN'             => array(1.818,'ken'),
        'Length::KERAT'           => array(0.0286,'kerat'),
        'Length::KILOFOOT'        => array(304.8,'kft'),
        'Length::KILOMETER'       => array(0.001,'km'),
        'Length::KILOPARSEC'      => array(3.0856776e+19,'kpc'),
        'Length::KILOYARD'        => array(914.4,'kyd'),
        'Length::KIND'            => array(0.5,'kind'),
        'Length::KLAFTER'         => array(1.8965,'klafter'),
        'Length::KLAFTER_SWISS'   => array(1.8,'klafter'),
        'Length::KLICK'           => array(1000,'klick'),
        'Length::KYU'             => array(0.00025,'kyu'),
        'Length::LAP_ANCIENT'     => array(402.336,''),
        'Length::LAP'             => array(400,'lap'),
        'Length::LAP_POOL'        => array(100,'lap'),
        'Length::LEAGUE_ANCIENT'  => array(2275,'league'),
        'Length::LEAGUE_NAUTIC'   => array(5556,'league'),
        'Length::LEAGUE_UK_NAUTIC'=> array(5559.552,'league'),
        'Length::LEAGUE'          => array(4828,'league'),
        'Length::LEAGUE_US'       => array(4828.0417,'league'),
        'Length::LEAP'            => array(2.0574,'leap'),
        'Length::LEGOA'           => array(6174.1,'legoa'),
        'Length::LEGUA'           => array(4200,'legua'),
        'Length::LEGUA_US'        => array(4233.4,'legua'),
        'Length::LEGUA_SPAIN_OLD' => array(4179.4,'legua'),
        'Length::LEGUA_SPAIN'     => array(6680,'legua'),
        'Length::LI_ANCIENT'      => array(500,'li'),
        'Length::LI_IMPERIAL'     => array(644.65,'li'),
        'Length::LI'              => array(500,'li'),
        'Length::LIEUE'           => array(3898,'lieue'),
        'Length::LIEUE_METRIC'    => array(4000,'lieue'),
        'Length::LIEUE_NAUTIC'    => array(5556,'lieue'),
        'Length::LIGHT_SECOND'    => array(299792458,'light second'),
        'Length::LIGHT_MINUTE'    => array(17987547480,'light minute'),
        'Length::LIGHT_HOUR'      => array(1079252848800,'light hour'),
        'Length::LIGHT_DAY'       => array(25902068371200,'light day'),
        'Length::LIGHT_YEAR'      => array(9460528404879000,'ly'),
        'Length::LIGNE'           => array(0.0021167,'ligne'),
        'Length::LIGNE_SWISS'     => array(0.002256,'ligne'),
        'Length::LINE'            => array(0.0021167,'li'),
        'Length::LINE_SMALL'      => array(0.000635,'li'),
        'Length::LINK'            => array(array('' => 792,'/' => 3937),'link'),
        'Length::LINK_ENGINEER'   => array(0.3048,'link'),
        'Length::LUG'             => array(5.0292,'lug'),
        'Length::LUG_GREAT'       => array(6.4008,'lug'),
        'Length::MARATHON'        => array(42194.988,'marathon'),
        'Length::MARK_TWAIN'      => array(3.6576074,'mark twain'),
        'Length::MEGAMETER'       => array(1000000,'Mm'),
        'Length::MEGAPARSEC'      => array(3.085677e+22,'Mpc'),
        'Length::MEILE_AUSTRIAN'  => array(7586,'meile'),
        'Length::MEILE'           => array(7412.7,'meile'),
        'Length::MEILE_GERMAN'    => array(7532.5,'meile'),
        'Length::METER'           => array(1,'m'),
        'Length::METRE'           => array(1,'m'),
        'Length::METRIC_MILE'     => array(1500,'metric mile'),
        'Length::METRIC_MILE_US'  => array(1600,'metric mile'),
        'Length::MICROINCH'       => array(2.54e-08,'µin'),
        'Length::MICROMETER'      => array(0.000001,'µm'),
        'Length::MICROMICRON'     => array(1.0e-12,'µµ'),
        'Length::MICRON'          => array(0.000001,'µ'),
        'Length::MIGLIO'          => array(1488.6,'miglio'),
        'Length::MIIL'            => array(7500,'miil'),
        'Length::MIIL_DENMARK'    => array(7532.5,'miil'),
        'Length::MIIL_SWEDISH'    => array(10687,'miil'),
        'Length::MIL'             => array(0.0000254,'mil'),
        'Length::MIL_SWEDISH'     => array(10000,'mil'),
        'Length::MILE_UK'         => array(1609,'mi'),
        'Length::MILE_IRISH'      => array(2048,'mi'),
        'Length::MILE'            => array(1609.344,'mi'),
        'Length::MILE_NAUTIC'     => array(1852,'mi'),
        'Length::MILE_NAUTIC_UK'  => array(1853.184,'mi'),
        'Length::MILE_NAUTIC_US'  => array(1852,'mi'),
        'Length::MILE_ANCIENT'    => array(1520,'mi'),
        'Length::MILE_SCOTTISH'   => array(1814,'mi'),
        'Length::MILE_STATUTE'    => array(1609.344,'mi'),
        'Length::MILE_US'         => array(array('' => 6336000,'/' => 3937),'mi'),
        'Length::MILHA'           => array(2087.3,'milha'),
        'Length::MILITARY_PACE'   => array(0.762,'mil. pace'),
        'Length::MILITARY_PACE_DOUBLE' => array(0.9144,'mil. pace'),
        'Length::MILLA'           => array(1392,'milla'),
        'Length::MILLE'           => array(1949,'mille'),
        'Length::MILLIARE'        => array(0.001478,'milliare'),
        'Length::MILLIMETER'      => array(0.001,'mm'),
        'Length::MILLIMICRON'     => array(1.0e-9,'mµ'),
        'Length::MKONO'           => array(0.4572,'mkono'),
        'Length::MOOT'            => array(0.0762,'moot'),
        'Length::MYRIAMETER'      => array(10000,'mym'),
        'Length::NAIL'            => array(0.05715,'nail'),
        'Length::NANOMETER'       => array(1.0e-9,'nm'),
        'Length::NANON'           => array(1.0e-9,'nanon'),
        'Length::PACE'            => array(1.524,'pace'),
        'Length::PACE_ROMAN'      => array(1.48,'pace'),
        'Length::PALM_DUTCH'      => array(0.10,'palm'),
        'Length::PALM_UK'         => array(0.075,'palm'),
        'Length::PALM'            => array(0.2286,'palm'),
        'Length::PALMO_PORTUGUESE'=> array(0.22,'palmo'),
        'Length::PALMO'           => array(0.20,'palmo'),
        'Length::PALMO_US'        => array(0.2117,'palmo'),
        'Length::PARASANG'        => array(6000,'parasang'),
        'Length::PARIS_FOOT'      => array(0.3248406,'paris foot'),
        'Length::PARSEC'          => array(3.0856776e+16,'pc'),
        'Length::PE'              => array(0.33324,'pé'),
        'Length::PEARL'           => array(0.001757299,'pearl'),
        'Length::PERCH'           => array(5.0292,'perch'),
        'Length::PERCH_IRELAND'   => array(6.4008,'perch'),
        'Length::PERTICA'         => array(2.96,'pertica'),
        'Length::PES'             => array(0.2967,'pes'),
        'Length::PETAMETER'       => array(1.0e+15,'Pm'),
        'Length::PICA'            => array(0.0042175176,'pi'),
        'Length::PICOMETER'       => array(1.0e-12,'pm'),
        'Length::PIE_ARGENTINA'   => array(0.2889,'pie'),
        'Length::PIE_ITALIC'      => array(0.298,'pie'),
        'Length::PIE'             => array(0.2786,'pie'),
        'Length::PIE_US'          => array(0.2822,'pie'),
        'Length::PIED_DE_ROI'     => array(0.3248406,'pied de roi'),
        'Length::PIK'             => array(0.71,'pik'),
        'Length::PIKE'            => array(0.71,'pike'),
        'Length::POINT_ADOBE'     => array(array('' => 0.3048, '/' => 864),'pt'),
        'Length::POINT'           => array(0.00035,'pt'),
        'Length::POINT_DIDOT'     => array(0.000377,'pt'),
        'Length::POINT_TEX'       => array(0.0003514598035,'pt'),
        'Length::POLE'            => array(5.0292,'pole'),
        'Length::POLEGADA'        => array(0.02777,'polegada'),
        'Length::POUCE'           => array(0.02707,'pouce'),
        'Length::PU'              => array(1.7907,'pu'),
        'Length::PULGADA'         => array(0.02365,'pulgada'),
        'Length::PYGME'           => array(0.346,'pygme'),
        'Length::Q'               => array(0.00025,'q'),
        'Length::QUADRANT'        => array(10001300,'quad'),
        'Length::QUARTER'         => array(402.336,'Q'),
        'Length::QUARTER_CLOTH'   => array(0.2286,'Q'),
        'Length::QUARTER_PRINT'   => array(0.00025,'Q'),
        'Length::RANGE'           => array(array('' => 38016000,'/' => 3937),'range'),
        'Length::REED'            => array(2.679,'reed'),
        'Length::RI'              => array(3927,'ri'),
        'Length::RIDGE'           => array(6.1722,'ridge'),
        'Length::RIVER'           => array(2000,'river'),
        'Length::ROD'             => array(5.0292,'rd'),
        'Length::ROD_SURVEY'      => array(array('' => 19800, '/' => 3937),'rd'),
        'Length::ROEDE'           => array(10,'roede'),
        'Length::ROOD'            => array(3.7783,'rood'),
        'Length::ROPE'            => array(3.7783,'rope'),
        'Length::ROYAL_FOOT'      => array(0.3248406,'royal foot'),
        'Length::RUTE'            => array(3.75,'rute'),
        'Length::SADZHEN'         => array(2.1336,'sadzhen'),
        'Length::SAGENE'          => array(2.1336,'sagene'),
        'Length::SCOTS_FOOT'      => array(0.30645,'scots foot'),
        'Length::SCOTS_MILE'      => array(1814.2,'scots mile'),
        'Length::SEEMEILE'        => array(1852,'seemeile'),
        'Length::SHACKLE'         => array(27.432,'shackle'),
        'Length::SHAFTMENT'       => array(0.15124,'shaftment'),
        'Length::SHAFTMENT_ANCIENT' => array(0.165,'shaftment'),
        'Length::SHAKU'           => array(0.303,'shaku'),
        'Length::SIRIOMETER'      => array(1.4959787e+17,'siriometer'),
        'Length::SMOOT'           => array(1.7018,'smoot'),
        'Length::SPAN'            => array(0.2286,'span'),
        'Length::SPAT'            => array(1.0e+12,'spat'),
        'Length::STADIUM'         => array(185,'stadium'),
        'Length::STEP'            => array(0.762,'step'),
        'Length::STICK'           => array(3.048,'stk'),
        'Length::STORY'           => array(3.3,'story'),
        'Length::STRIDE'          => array(1.524,'stride'),
        'Length::STRIDE_ROMAN'    => array(1.48,'stride'),
        'Length::TENTHMETER'      => array(1.0e-10,'tenth-meter'),
        'Length::TERAMETER'       => array(1.0e+12,'Tm'),
        'Length::THOU'            => array(0.0000254,'thou'),
        'Length::TOISE'           => array(1.949,'toise'),
        'Length::TOWNSHIP'        => array(array('' => 38016000,'/' => 3937),'twp'),
        'Length::T_SUN'           => array(0.0358,"t'sun"),
        'Length::TU'              => array(161130,'tu'),
        'Length::TWAIN'           => array(3.6576074,'twain'),
        'Length::TWIP'            => array(0.000017639,'twip'),
        'Length::U'               => array(0.04445,'U'),
        'Length::VARA_CALIFORNIA' => array(0.83820168,'vara'),
        'Length::VARA_MEXICAN'    => array(0.83802,'vara'),
        'Length::VARA_PORTUGUESE' => array(1.10,'vara'),
        'Length::VARA_AMERICA'    => array(0.864,'vara'),
        'Length::VARA'            => array(0.83587,'vara'),
        'Length::VARA_TEXAS'      => array(0.84666836,'vara'),
        'Length::VERGE'           => array(0.9144,'verge'),
        'Length::VERSHOK'         => array(0.04445,'vershok'),
        'Length::VERST'           => array(1066.8,'verst'),
        'Length::WAH'             => array(2,'wah'),
        'Length::WERST'           => array(1066.8,'werst'),
        'Length::X_UNIT'          => array(1.0020722e-13,'Xu'),
        'Length::YARD'            => array(0.9144,'yd'),
        'Length::YOCTOMETER'      => array(1.0e-24,'ym'),
        'Length::YOTTAMETER'      => array(1.0e+24,'Ym'),
        'Length::ZEPTOMETER'      => array(1.0e-21,'zm'),
        'Length::ZETTAMETER'      => array(1.0e+21,'Zm'),
        'Length::ZOLL'            => array(0.02634,'zoll'),
        'Length::ZOLL_SWISS'      => array(0.03,'zoll')
    );

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
    public function __construct($value, $type, $locale)
    {
        $this->setValue($value, $type, $locale);
    }


    /**
     * Compare if the value and type is equal
     *
     * @return boolean
     */
    public function equals($object)
    {
        if ($object->toString() == $this->toString())
        {
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
    public function setValue($value, $type, $locale)
    {
        $value = Zend_Locale_Format::getNumber($value, $locale);
        if (empty(self::$_UNITS[$type]))
            self::throwException('unknown type of length:'.$type);
        parent::setValue($value);
        parent::setType($type);
    }


    /**
     * Set a new type, and convert the value
     *
     * @throws Zend_Measure_Exception
     */
    public function setType($type)
    {
        if (empty(self::$_UNITS[$type]))
            self::throwException('unknown type of length:'.$type);

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
        parent::setValue($value);
        parent::setType($type);
    }


    /**
     * Returns a string representation
     *
     * @return string
     */
    public function toString()
    {
        return parent::getValue().' '.self::$_UNITS[parent::getType()][1];
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
}
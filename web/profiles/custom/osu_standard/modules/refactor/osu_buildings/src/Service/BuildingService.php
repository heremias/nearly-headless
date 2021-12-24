<?php

namespace Drupal\osu_buildings\Service;

/**
 * Provides a service to lookup buildings.
 */
class BuildingService implements \Drupal\osu_buildings\Service\BuildingServiceInterface {

  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public function map() {
    return [
      '930' => '1618 Highland St',
      '908' => '1664-1668 Neil Ave',
      '988' => '1991 Kenny Rd.',
      '191' => '4-H Center',
      '2443' => 'Ackerman - Office and Gas House',
      '921' => 'Ackerman Place',
      '241' => 'Ackerman Rd, 650',
      '242' => 'Ackerman Rd, 660',
      '2442' => 'Ackerman Rd, 690 - Shelter House',
      '658' => 'Adena Hall',
      '400' => 'Administration Building',
      '909' => 'Adriatico\'s',
      '211' => 'Adventure Recreation Center',
      '199' => 'Aerospace Research Center',
      '003' => 'Agricultural Administration',
      '298' => 'Agricultural Engineering Building',
      '425' => 'Agronomy Service',
      '021' => 'Airport Administration',
      '955' => 'Airport Dr, 2740',
      '031' => 'Airport Operations',
      '466' => 'Anaerobic Digester Plant',
      '174' => 'Animal House Kinnear Research Center',
      '156' => 'Animal Science Building',
      '647' => 'Apple Creek - Dairy Barn',
      '590' => 'Apple Creek - Equine Stable',
      '8116' => 'Apple Creek - Feedlot Barn',
      '667' => 'Apple Creek - Land Laboratory Barn',
      '261' => 'Archer House',
      '131' => 'Aronoff Laboratory',
      '011' => 'Arps Hall',
      '595' => 'ATI and OARDC Apartment Village Administration',
      '646' => 'ATI Student Activities Center',
      '306' => 'Atwell Hall',
      '453' => 'Badger Farm - Barn',
      '095' => 'Baker Hall',
      '280' => 'Baker Systems Engineering',
      '487' => 'Barnhardt Rice House',
      '184' => 'Barrett House',
      '013' => 'Battelle Building (L)',
      '928' => 'Beavercreek',
      '458' => 'Beef Research Center',
      '307' => 'Bevis Hall',
      '228' => 'Bill Davis Baseball Stadium',
      '345' => 'Biocontainment Laboratory',
      '276' => 'Biological Science Building',
      '010' => 'Biological Sciences Greenhouses',
      '112' => 'Biomedical Research Tower',
      '181' => 'Blackburn House',
      '1107' => 'Blackburn House',
      '254' => 'Blackwell Inn',
      '360' => 'Blankenship Hall',
      '315' => 'Bloch Cancer Survivors Plaza',
      '146' => 'Bolz Hall',
      '1104' => 'Bowen House',
      '097' => 'Bradley Hall',
      '372' => 'Brain and Spine Hospital',
      '001' => 'Bricker Hall',
      '016' => 'Brown Hall',
      '200888' => 'Browning Amphitheater',
      '872' => 'Bruegger\'s Bagels',
      '108' => 'Buckeye Field',
      '801' => 'Buckeye Village A - Cuyahoga Ct, 600-626',
      '833' => 'Buckeye Village Administration',
      '800' => 'Buckeye Village Community Center',
      '834' => 'Buckeye Village Recreation Hall',
      '1105' => 'Busch House',
      '026' => 'Caldwell Laboratory',
      '018' => 'Campbell Hall',
      '200606' => 'Campus Gateway Barnes & Noble',
      '987' => 'Campus Shop',
      '201501' => 'CampusParc',
      '098' => 'Canfield Hall',
      '248' => 'CBEC',
      '371' => 'Celeste Laboratory of Chemistry',
      '213' => 'Center for Integrative Medicine',
      '934' => 'Center of Science and Industry - COSI',
      '077' => 'Central Service Building',
      '200607' => 'Chadwick Arboretum',
      '385' => 'Child Care Center',
      '063' => 'Cockins Hall',
      '255' => 'Coffey Rd Sports Center',
      '380' => 'Community Extension Center',
      '467' => 'Compost and Biomass Utility Research Building',
      '363' => 'Comprehensive Cancer Center',
      '641' => 'Conservatory Greenhouse',
      '056' => 'Converse Hall',
      '676' => 'COTC - LeFevre Hall',
      '140' => 'Cryogenic Laboratory',
      '293' => 'Cunz Hall',
      '1106' => 'Curl Hall',
      '008' => 'Dairy Loose Housing Barn',
      '023' => 'Dairy Research Barn',
      '113' => 'Davis Heart and Lung Research Institute',
      '382' => 'Davis Medical Research Center',
      '1012' => 'Davis Tower',
      '030' => 'Denney Hall',
      '025' => 'Derby Hall',
      '089' => 'Doan Hall',
      '171' => 'Dodd Hall',
      '985' => 'Dodridge St, 250 W',
      '2563' => 'Doric on Lane',
      '189' => 'Drackett Tower',
      '296' => 'Drake Performance and Event Center',
      '279' => 'Dreese Laboratories',
      '049' => 'Drinko Hall',
      '337' => 'Dulles Hall',
      '438' => 'Edgington Hall',
      '057' => 'Edison Joining Technology Center',
      '004' => 'Eighteenth Ave, 209 W',
      '005' => 'Eighteenth Avenue Library',
      '009' => 'ElectroScience Laboratory',
      '977' => 'ElectroScience Laboratory Complex',
      '177' => 'Eleventh Ave, 235-243 W',
      '193' => 'Eleventh Ave, 33 W',
      '964' => 'Eleventh Ave, 45 W',
      '902' => 'Eleventh Ave, 53 W',
      '072' => 'Enarson Classroom Building',
      '995' => 'Evans Hall',
      '150' => 'Evans Laboratory',
      '028' => 'Faculty Club',
      '284' => 'Fawcett Center for Tomorrow',
      '040' => 'Fechko Alumnae Scholarship House',
      '501' => 'Feedstock Processing Research Facility',
      '855' => 'Fifteenth Ave, 141 E',
      '403' => 'Fisher Auditorium',
      '2565' => 'Fisher Commons',
      '200896' => 'Fisher Commons',
      '249' => 'Fisher Hall',
      '235' => 'Flight Laboratory',
      '151' => 'Fontana Laboratories',
      '9401' => 'Food Agriculture and Biological Engineering',
      '547' => 'Food Animal Health Research Building',
      '628' => 'Founders Hall',
      '201201' => 'Fred Beekman Park',
      '086' => 'French Field House',
      '059' => 'Fry Hall',
      '282' => 'Galbreath Equine Center',
      '860' => 'Gateway A',
      '862' => 'Gateway B',
      '863' => 'Gateway C',
      '865' => 'Gateway D',
      '867' => 'Gateway F - North',
      '868' => 'Gateway F - South',
      '250' => 'Gerlach Hall',
      '404' => 'Gerlaugh Hall',
      '965' => 'German House',
      '180' => 'Goss Laboratory',
      '405' => 'Gourley Hall',
      '277' => 'Graves Hall',
      '636' => 'Greenhouse',
      '037' => 'Hagerty Hall',
      '085' => 'Hale Hall',
      '185' => 'Halloran House',
      '635' => 'Halterman Hall',
      '038' => 'Hamilton Hall',
      '864' => 'Hanley Alumnae Scholarship House',
      '165' => 'Harding Hospital',
      '182' => 'Haverfield House',
      '664' => 'Hawk\'s Nest Golf Club - Hawkins Clubhouse',
      '406' => 'Hayden Hall',
      '039' => 'Hayes Hall',
      '222' => 'Heffner Wetland Research and Education',
      '132' => 'Herrick Dr, 393',
      '927' => 'Highland St, 1615',
      '274' => 'Hitchcock Hall',
      '657' => 'Hopewell Hall',
      '149' => 'Hopkins Hall',
      '194' => 'Houck House',
      '1102' => 'Houston House',
      '297' => 'Howlett Greenhouses',
      '295' => 'Howlett Hall',
      '042' => 'Hughes Hall',
      '229' => 'Ice Rink',
      '338' => 'Independence Hall',
      '878' => 'Institute for Behavioral Medicine Research',
      '375' => 'James Cancer Hospital',
      '1004' => 'Jameson Crane Sports Medicine Institute',
      '440' => 'Japanese Beetle Laboratory',
      '014' => 'Jennings Hall',
      '092' => 'Jesse Owens Memorial Stadium',
      '347' => 'Jesse Owens Recreation Center North',
      '348' => 'Jesse Owens Recreation Center South',
      '349' => 'Jesse Owens Tennis Center West',
      '201202' => 'Jesse Owens West Park',
      '267' => 'Jones Tower',
      '046' => 'Journalism Building',
      '2574' => 'Kappa Kappa Gamma House',
      '105' => 'Kennedy Commons',
      '232' => 'Kenny Rd, 1900',
      '214' => 'Kenny Rd, 2006-2030',
      '033' => 'Kepler Club House',
      '364' => 'Kinnear Rd Center A',
      '365' => 'Kinnear Rd Center B',
      '366' => 'Kinnear Rd Center C',
      '367' => 'Kinnear Rd Center D',
      '368' => 'Kinnear Rd Center E',
      '373' => 'Kinnear Rd, 1100',
      '932' => 'Kinnear Rd, 1165',
      '378' => 'Kinnear Rd, 1212-1218',
      '374' => 'Kinnear Rd, 1224',
      '1260' => 'Kinnear Rd, 1245-1255',
      '128' => 'Kinnear Rd, 1260',
      '395' => 'Kinnear Rd, 1275-1305',
      '126' => 'Kinnear Rd, 1314',
      '951' => 'Kinnear Rd, 1315',
      '963' => 'Kinnear Rd, 760',
      '227' => 'Kinnear Rd, 930',
      '931' => 'Kinnear Rd, 960',
      '961' => 'Knight House',
      '017' => 'Knowlton Hall',
      '340' => 'Kottman Hall',
      '426' => 'Krauss Dairy Research Facility',
      '959' => 'Kuhn Honors and Scholars House',
      '915' => 'Lane Ave, 121 W',
      '917' => 'Lane Ave, 127 W',
      '898' => 'Lane Ave, 1480 W',
      '911' => 'Lane Avenue: 133 West',
      '289' => 'Laundry Building',
      '891' => 'Lawrence Tower',
      '041' => 'Lazenby Hall',
      '350' => 'Library Book Depository',
      '271' => 'Lincoln Tower',
      '201203' => 'Lincoln Tower Park',
      '022' => 'Longaberger Alumni House',
      '051' => 'Lord Hall',
      '100' => 'Mack Hall',
      '265' => 'MacQuigg Laboratory',
      '078' => 'Maintenance Building',
      '252' => 'Mason Hall',
      '187' => 'Mathematics Building',
      '007' => 'Mathematics Tower',
      '303' => 'McCampbell Hall',
      '247' => 'McCorkle Aquatic Pavilion',
      '069' => 'McCracken Power Plant',
      '053' => 'McPherson Chemical Laboratory',
      '9370' => 'Meeting House',
      '281' => 'Meiling Hall',
      '9343' => 'Mellinger Farm - Residence House 1',
      '054' => 'Mendenhall Laboratory',
      '260' => 'Mendoza House',
      '055' => 'Mershon Auditorium',
      '047' => 'Mershon Center',
      '943' => 'Metro High School',
      '200602' => 'Mirror Lake',
      '882' => 'Morehouse Medical Plaza - Concourse',
      '880' => 'Morehouse Medical Plaza - Pavilion',
      '881' => 'Morehouse Medical Plaza - Tower',
      '272' => 'Morrill Tower',
      '190' => 'Morrison Tower',
      '311' => 'Mount Hall',
      '912' => 'Neil Ave, 1656-1660',
      '147' => 'Newman and Wolfrom Laboratory of Chemistry',
      '275' => 'Newton Hall',
      '091' => 'Nicklaus Museum',
      '145' => 'Nineteenth Ave, 140 W',
      '192' => 'North Commons',
      '1109' => 'North Recreation Center',
      '094' => 'North Star Rd, 2470',
      '357' => 'Northwood-High Building',
      '186' => 'Norton House',
      '2564' => 'Norwich Flats',
      '1110' => 'Nosker House',
      '262' => 'Nosker House',
      '082' => 'Ohio Stadium',
      '161' => 'Ohio Union',
      '012' => 'Ornamental Plant Germplasm Center',
      '060' => 'Orton Hall',
      '967' => 'OSU Center for Human Resource Research',
      '2501' => 'OSU Internal Medicine and Pediatrics',
      '102' => 'Oxley Hall',
      '061' => 'Page Hall',
      '851' => 'Park-Stradley Hall',
      '064' => 'Parker Food Science and Technology',
      '278' => 'Parking Garage - Arps',
      '283' => 'Parking Garage - Biological Science Building',
      '172' => 'Parking Garage - Cannon Dr N and S',
      '352' => 'Parking Garage - Eleventh Ave',
      '866' => 'Parking Garage - Gateway E',
      '159' => 'Parking Garage - Lane Ave',
      '287' => 'Parking Garage - Neil Ave',
      '875' => 'Parking Garage - Ninth Ave E',
      '359' => 'Parking Garage - Ninth Ave W',
      '083' => 'Parking Garage - Northwest',
      '288' => 'Parking Garage - Ohio Union North',
      '162' => 'Parking Garage - Ohio Union South',
      '170' => 'Parking Garage - SafeAuto Hospitals',
      '088' => 'Parking Garage - Tuttle Park Pl',
      '387' => 'Parking Garage - Twelfth Ave',
      '892' => 'Parking Garage - W Lane Ave',
      '273' => 'Parks Hall',
      '103' => 'Paterson Hall',
      '989' => 'Peach Point - Shop',
      '048' => 'Pennsylvania Place',
      '3000' => 'Perry Webb Student Life Building',
      '253' => 'Pfahl Hall',
      '245' => 'Physical Activity and Education Services - PAES',
      '070' => 'Physics Research Building',
      '543' => 'Plant and Animal Agrosecurity Research Facility',
      '066' => 'Plumb Hall',
      '869' => 'Pomerene Alumnae Scholarship House',
      '067' => 'Pomerene Hall',
      '024' => 'Postle Hall',
      '469' => 'Poultry Service Building',
      '402' => 'Pounden Hall',
      '309' => 'Pressey Hall',
      '290' => 'Printing Facility',
      '302' => 'Prior Hall',
      '144' => 'Psychology Building',
      '155' => 'Radiation Dosimetry Calibration Facility',
      '090' => 'Ramseyer Hall',
      '269' => 'Raney Commons',
      '1103' => 'Raney House',
      '246' => 'Recreation and Physical Activity Center',
      '585' => 'Reese Center',
      '073' => 'Research Center',
      '200' => 'Research Foundation',
      '409' => 'Research Operations Service Building',
      '549' => 'Research Services Building',
      '850' => 'Residences on Tenth',
      '354' => 'Rhodes Hall',
      '266' => 'Riffe Building',
      '308' => 'Rightmire Hall',
      '969' => 'Riverwatch Tower',
      '353' => 'Ross Heart Hospital',
      '384' => 'Rothenbuhler Honey Bee Research Laboratory',
      '215' => 'Sandefur Wetland Pavilion',
      '264' => 'Satellite Communications Facility',
      '933' => 'Schoenbaum Family Center',
      '251' => 'Schoenbaum Hall',
      '081' => 'Schottenstein Center',
      '1011' => 'Schumaker Student-Athlete Development Complex',
      '974' => 'Science Village',
      '310' => 'Scott Hall',
      '1108' => 'Scott House',
      '148' => 'Scott Laboratory',
      '410' => 'Selby Hall',
      '471' => 'Sheep Barn 2',
      '358' => 'Sherman Studio Art Center',
      '679' => 'Shisler Center for Education and Economic Development',
      '099' => 'Siebert Hall',
      '9118' => 'Simon Rice House',
      '080' => 'Sisson Hall',
      '643' => 'Skou Hall',
      '109' => 'Smith Hall',
      '065' => 'Smith Laboratory',
      '852' => 'Smith-Steeb Hall',
      '874' => 'Spielman Comprehensive Breast Center',
      '076' => 'St John Arena',
      '176' => 'Starling Loving Hall',
      '949' => 'State of Ohio Computer Center',
      '141' => 'Steeb Hall',
      '944' => 'Steelwood Athletic Training Facility',
      '084' => 'Stillman Hall',
      '381' => 'Stores and Receiving',
      '160' => 'Student Academic Services',
      '106' => 'Sullivant Hall',
      '268' => 'Taylor Tower',
      '379' => 'Telecommunications Network Center',
      '200605' => 'The Oval',
      '050' => 'Thompson Library',
      '412' => 'Thorne Hall',
      '1101' => 'Torres House',
      '087' => 'Townshend Hall',
      '984' => 'Transmitter - WOSU',
      '966' => 'Transplant Services at Kinnear Rd, 770',
      '983' => 'Turfgrass Foundation Research and Education Facility',
      '356' => 'Twelfth Ave, 395 W',
      '163' => 'Tzagournis Medical Research Facility',
      '339' => 'University Hall',
      '200603' => 'University Plaza Hotel',
      '991' => 'Urban Arts Space',
      '137' => 'Van De Graaff Laboratory',
      '299' => 'Veterinary Medical Center',
      '136' => 'Veterinary Medicine Academic',
      '583' => 'Warner Library and Student Center',
      '952' => 'Waterman Farm - Agronomy Field Greenhouse',
      '937' => 'Waterman Farm - Agronomy Pole Barn',
      '992' => 'Waterman Farm - Agronomy Turf Research',
      '316' => 'Waterman Farm - Dairy Calf Barn',
      '221' => 'Waterman Farm - Heifer Barn',
      '179' => 'Waterman Farm - Laboratory Headquarters',
      '317' => 'Waterman Farm - Main Dairy Barn',
      '107' => 'Watts Hall',
      '355' => 'Weigel Hall',
      '210' => 'Wetland Bike Shelter',
      '386' => 'Wexner Center for the Arts',
      '294' => 'Wilce Student Health Center',
      '846' => 'William Hall Complex - Neil Building',
      '848' => 'William Hall Complex - Scholars House East',
      '847' => 'William Hall Complex - Scholars House West',
      '849' => 'William Hall Complex - Worthington Building',
      '414' => 'Williams Hall',
      '157' => 'Wiseman Hall',
      '029' => 'Women\'s Field House',
      '270' => 'Woody Hayes Athletic Center',
      '052' => 'Younkin Success Center',
      '231' => 'Zoology Research Laboratory',
    ];
  }

}
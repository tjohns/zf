--
-- Table structure for table beds
--

CREATE TABLE zfclinic_beds (
  id SERIAL,
  station_id int NOT NULL,
  PRIMARY KEY  (id)
) ;

-- --------------------------------------------------------

--
-- Table structure for table occupancies
--

CREATE TABLE zfclinic_occupancies (
  id SERIAL,
  patient_id int NOT NULL,
  bed_id int NOT NULL,
  station_id int NOT NULL,
  occupied_from date NOT NULL,
  occupied_to date NOT NULL,
  PRIMARY KEY  (id)
) ;

-- --------------------------------------------------------

--
-- Table structure for table patients
--

CREATE TABLE zfclinic_patients (
  id SERIAL,
  name varchar(255) NOT NULL,
  social_security_number varchar(16) NOT NULL,
  birth_date date NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE (social_security_number)
);

-- --------------------------------------------------------

--
-- Table structure for table stations
--

CREATE TABLE zfclinic_stations (
  id SERIAL,
  name varchar(64) NOT NULL,
  PRIMARY KEY  (id)
);

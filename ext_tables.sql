#
# Table structure for table 'tx_hsmail_config'
#
CREATE TABLE tx_hsmail_config
(
	id int(11) NOT NULL auto_increment,
	config varchar(255) DEFAULT '' NOT NULL,
	value varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (id)
);

#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content
(
	tx_hsmail_form int(11) DEFAULT 0 NOT NULL
);

#
# Table structure for table 'tx_hsmail_domain_model_formconfig'
#
CREATE TABLE tx_hsmail_domain_model_formconfig (

	id varchar(255) DEFAULT '' NOT NULL,
	random_id text,
	title text

);

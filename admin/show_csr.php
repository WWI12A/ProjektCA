<?php

error_reporting(E_ALL);
    include("../mysql.php");
    include("../functions.php");

    session_start();
    include("../autologout.php");
	


    // Prüfen, ob der User den Adminbereich betreten darf
    if(!isset($_SESSION['Rechte']) OR !in_array('Adminbereich', $_SESSION['Rechte']))
        die("Sie haben keine Berechtigung, diese Seite zu betreten!\n");
	
	
//SQL-Abfrage, um benötigte Parameter zu holen
$abfrage = "SELECT * FROM signing_requests WHERE RequestID ='".$_POST['csr']."'";

$ergebnis = mysql_query($abfrage);
while($row = mysql_fetch_object($ergebnis)){

//Übergabe von Pfad zur CSR 7Nickname / RequestID aus der Datenbank an Variablen + anderes Angaben (SANs...)
$pfad = $row->CSR_Path;
$nickname = $row->Nickname;	
$RequestID = $row->RequestID;
$CertType = $row->CertType;
$SANs = $row->SAN_Value;
	
}
	
//Parsen des CSR-Strings, um benötigte Subjects zu erhalten und Übergabe an Variablen
$out=array();
exec('openssl req -in '.$pfad.' -text -noout',$out);


$Land = preg_split("/,/", explode('C=', $out[3])[1]);

$Bundesland = preg_split("/, /", explode('ST=', $out[3])[1]);

$Stadt = preg_split("/, /", explode('L=', $out[3])[1]);

$Organisation = preg_split("/, /", explode('O=', $out[3])[1]);

//Prüfen ob OU leer istLa
if(strpos ( $out[3] , 'OU=') != FALSE){
$Einheit = preg_split("/, /", explode('OU=', $out[3])[1]);
} else {
	$Einheit[0] = "' '";
}
$CommonName = preg_split("#/#", explode('CN=', $out[3])[1]);

$Email = preg_split("/ /", explode('emailAddress=', $out[3])[1]);
	

//Form um die Werte des CSR anzuzeigen und gegebenenfalls zu ändern

echo "<form action='zertifikat_erstellen.php' method='post' >" ; //Hier fehlt noch der passende Aufruf von zertifikat_erstellen.php    
echo" <table>";
echo "<tr style=\"color:red\">";
echo "<td>Zertifikatart:</td>";
echo "<td>$CertType<td/>";
echo "</tr>";
echo "<tr>";
echo "<td>Land:</td>";
echo "<td><input name='Land' value='".$Land[0]."' type='text' size='30' maxlength='50'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Bundesland:</td>";
echo "<td><input name='Bundesland' value='".$Bundesland[0]."' type='text' size='30' maxlength='30'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Stadt:</td>";
echo "<td><input name='Stadt' value='".$Stadt[0]."' type='text' size='30' maxlength='30'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Organisation:</td>";
echo "<td><input name='Organisation' value='".$Organisation[0]."'  size='30' maxlength='30'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Organisationseinheit:</td>";
echo "<td><input name='Einheit' value='".$Einheit[0]."'  type='text' size='30' maxlength='30'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>CommonName:</td>";
echo "<td><input name='CommonName' value='".$CommonName[0]."' type='text' size='30' maxlength='30'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Email:</td>";
echo "<td><input name='email' value='".$Email[0]."' type='text' size='30' maxlength='30'></td>";
echo "</tr>";
if ($CertType == "SAN") {
	
	echo "<tr>";
	echo "<td><input type='hidden' name='CertType' value='SAN'>SANs:</td>";
	echo "<td><input name='SANs' value='".$SANs."' type='text' size='30' maxlength='120'></td>";
	echo "</tr>";
	
}
echo "<tr>";
echo "<td>Genehmigen:</td>";
echo "<td><button type='submit' name='genehmigen' value = ".$RequestID." >Genehmigen </button><td/>";
echo "</tr>";
echo "</table>";
echo"</form>";	
	

	
echo '<strong>Pfad zur CSR:</strong>';
echo "<br/>";
echo $pfad; 
echo "<br/>";
echo "<br/>";


/*-------------------------------------Kann raus------------------------
echo $Land[0];
echo "<br/>";
echo $Bundesland[0];
echo "<br/>";
echo $Stadt[0];
echo "<br/>";
echo $Organisation[0];
echo "<br/>";
echo $Einheit[0];
echo "<br/>";
echo $CommonName[0];
echo "<br/>";
echo $Email[0];
echo "<br/>";


echo "<br/>";
echo "<br/>";
echo "<br/>";
//print_r(explode('C=', $test));
-------------------------------------------------------------------*/


echo "<strong>Ausgabe der kompletten CSR:</strong>";
echo "<br/>";
passthru('openssl req -in '.$pfad.' -text -noout');
//$test2 = $test;

///////////////////////////////////////Prüfen ob SAN-Zertifikat, wenn ja, dann erstellen der entsprechenden CONFIG-Datei////////////////////////

if ($CertType == "SAN") {
	
	
	$conftext = "#
# OpenSSL example configuration file.
# This is mostly being used for generation of certificate requests.
#

# This definition stops the following lines choking if HOME isn't
# defined.
HOME			= .
RANDFILE		= \$ENV::HOME/.rnd

# Extra OBJECT IDENTIFIER info:
#oid_file		= \$ENV::HOME/.oid
oid_section		= new_oids

# To use this configuration file with the \"-extfile\" option of the
# \"openssl x509\" utility, name here the section containing the
# X.509v3 extensions to use:
# extensions		= 
# (Alternatively, use a configuration file that has only
# X.509v3 extensions in its main [= default] section.)

[ new_oids ]

# We can add new OIDs in here for use by 'ca', 'req' and 'ts'.
# Add a simple OID like this:
# testoid1=1.2.3.4
# Or use config file substitution like this:
# testoid2=\${testoid1}.5.6

# Policies used by the TSA examples.
tsa_policy1 = 1.2.3.4.1
tsa_policy2 = 1.2.3.4.5.6
tsa_policy3 = 1.2.3.4.5.7

####################################################################
[ ca ]
default_ca	= CA_default		# The default ca section

####################################################################
[ CA_default ]

dir		= C:/Users/Administrator/Documents/intermediate
certs		= \$dir/certs		# Where the issued certs are kept
crl_dir		= \$dir/crl		# Where the issued crl are kept
database	= \$dir/index.txt	# database index file.
#unique_subject	= no			# Set to 'no' to allow creation of
					# several ctificates with same subject.
new_certs_dir	= \$dir/newcerts		# default place for new certs.

certificate	= \$dir/intermediate.crt       	# The CA certificate
serial		= \$dir/serial 		# The current serial number
crlnumber	= \$dir/crlnumber	# the current crl number
					# must be commented out to leave a V1 CRL
crl		= \$dir/crl.pem 		# The current CRL
private_key	= \$dir/private/intermediate.key   # The private key
RANDFILE	= \$dir/private/.rand	# private random number file

x509_extensions	= usr_cert		# The extentions to add to the cert

# Comment out the following two lines for the \"traditional\"
# (and highly broken) format.
name_opt 	= ca_default		# Subject Name options
cert_opt 	= ca_default		# Certificate field options

# Extension copying option: use with caution.
# copy_extensions = copy

# Extensions to add to a CRL. Note: Netscape communicator chokes on V2 CRLs
# so this is commented out by default to leave a V1 CRL.
# crlnumber must also be commented out to leave a V1 CRL.
# crl_extensions	= crl_ext

default_days	= 365			# how long to certify for
default_crl_days= 30			# how long before next CRL
default_md	= sha256		# use public key default MD
preserve	= no			# keep passed DN ordering

# A few difference way of specifying how similar the request should look
# For type CA, the listed attributes must be the same, and the optional
# and supplied fields are just that :-)
policy		= policy_match

# For the CA policy
[ policy_match ]
countryName = supplied
stateOrProvinceName = supplied
localityName = supplied
organizationName = supplied
organizationalUnitName = optional
commonName = optional
emailAddress = optional

# For the 'anything' policy
# At this point in time, you must list all acceptable 'object'
# types.
[ policy_anything ]
countryName		= optional
stateOrProvinceName	= optional
localityName		= optional
organizationName	= optional
organizationalUnitName	= optional
commonName		= supplied
emailAddress		= optional

####################################################################
[ req ]
default_bits		= 2048
default_keyfile 	= privkey.pem
distinguished_name	= req_distinguished_name
attributes		= req_attributes
x509_extensions	= v3_ca	# The extentions to add to the self signed cert

# Passwords for private keys if not present they will be prompted for
# input_password = secret
# output_password = secret

# This sets a mask for permitted string types. There are several options. 
# default: PrintableString, T61String, BMPString.
# pkix	 : PrintableString, BMPString (PKIX recommendation before 2004)
# utf8only: only UTF8Strings (PKIX recommendation after 2004).
# nombstr : PrintableString, T61String (no BMPStrings or UTF8Strings).
# MASK:XXXX a literal mask value.
# WARNING: ancient versions of Netscape crash on BMPStrings or UTF8Strings.
string_mask = utf8only

# req_extensions = v3_req # The extensions to add to a certificate request

[ req_distinguished_name ]
countryName			= Country Name (2 letter code)
countryName_default		= DE
countryName_min			= 2
countryName_max			= 2

stateOrProvinceName		= State or Province Name (full name)
stateOrProvinceName_default	= Baden-Wuerttemberg

localityName			= Locality Name (eg, city)
localityName_default		= Heidenheim
0.organizationName		= Organization Name (eg, company)
0.organizationName_default	= DHBW Heidenheim Certificate Authority

# we can do this but it is not needed normally :-)
#1.organizationName		= Second Organization Name (eg, company)
#1.organizationName_default	= World Wide Web Pty Ltd

organizationalUnitName		= Organizational Unit Name (eg, section)
#organizationalUnitName_default	=

commonName			= Common Name (e.g. server FQDN or YOUR name)
commonName_max			= 64

emailAddress			= Email Address
emailAddress_max		= 64

# SET-ex3			= SET extension number 3

[ req_attributes ]
challengePassword		= A challenge password
challengePassword_min		= 4
challengePassword_max		= 20

unstructuredName		= An optional company name

[ usr_cert ]

# These extensions are added when 'ca' signs a request.

# This goes against PKIX guidelines but some CAs do it and some software
# requires this to avoid interpreting an end user certificate as a CA.

basicConstraints=CA:FALSE

# Here are some examples of the usage of nsCertType. If it is omitted
# the certificate can be used for anything *except* object signing.

# This is OK for an SSL server.
# nsCertType			= server

# For an object signing certificate this would be used.
# nsCertType = objsign

# For normal client use this is typical
# nsCertType = client, email

# and for everything including object signing:
# nsCertType = client, email, objsign

# This is typical in keyUsage for a client certificate.
# keyUsage = nonRepudiation, digitalSignature, keyEncipherment

# This will be displayed in Netscape's comment listbox.
nsComment			= \"OpenSSL Generated Certificate\"

# PKIX recommendations harmless if included in all certificates.
subjectKeyIdentifier=hash
authorityKeyIdentifier=keyid,issuer

# This stuff is for subjectAltName and issuerAltname.
# Import the email address.
# subjectAltName=email:copy
# An alternative to produce certificates that aren't
# deprecated according to PKIX.
# subjectAltName=email:move

# Copy subject details
# issuerAltName=issuer:copy

#nsCaRevocationUrl		= http://www.domain.dom/ca-crl.pem
#nsBaseUrl
#nsRevocationUrl
#nsRenewalUrl
#nsCaPolicyUrl
#nsSslServerName

# This is required for TSA certificates.
# extendedKeyUsage = critical,timeStamping

[ v3_req ]

# Extensions to add to a certificate request

basicConstraints = CA:FALSE
keyUsage = nonRepudiation, digitalSignature, keyEncipherment

[ v3_ca ]


# Extensions for a typical CA


# PKIX recommendation.

subjectKeyIdentifier=hash

authorityKeyIdentifier=keyid:always,issuer

# This is what PKIX recommends but some broken software chokes on critical
# extensions.
#basicConstraints = critical,CA:true
# So we do this instead.
basicConstraints = CA:true

# Key usage: this is typical for a CA certificate. However since it will
# prevent it being used as an test self-signed certificate it is best
# left out by default.
# keyUsage = cRLSign, keyCertSign

# Some might want this also
# nsCertType = sslCA, emailCA

# Include email address in subject alt name: another PKIX recommendation
# subjectAltName=email:copy
# Copy issuer details
# issuerAltName=issuer:copy

# DER hex encoding of an extension: beware experts only!
# obj=DER:02:03
# Where 'obj' is a standard or added object
# You can even override a supported extension:
# basicConstraints= critical, DER:30:03:01:01:FF

[ crl_ext ]

# CRL extensions.
# Only issuerAltName and authorityKeyIdentifier make any sense in a CRL.

# issuerAltName=issuer:copy
authorityKeyIdentifier=keyid:always

[ proxy_cert_ext ]
# These extensions should be added when creating a proxy certificate

# This goes against PKIX guidelines but some CAs do it and some software
# requires this to avoid interpreting an end user certificate as a CA.

basicConstraints=CA:FALSE

# Here are some examples of the usage of nsCertType. If it is omitted
# the certificate can be used for anything *except* object signing.

# This is OK for an SSL server.
# nsCertType			= server

# For an object signing certificate this would be used.
# nsCertType = objsign

# For normal client use this is typical
# nsCertType = client, email

# and for everything including object signing:
# nsCertType = client, email, objsign

# This is typical in keyUsage for a client certificate.
# keyUsage = nonRepudiation, digitalSignature, keyEncipherment

# This will be displayed in Netscape's comment listbox.
nsComment			= \"OpenSSL Generated Certificate\"

# PKIX recommendations harmless if included in all certificates.
subjectKeyIdentifier=hash
authorityKeyIdentifier=keyid,issuer

# This stuff is for subjectAltName and issuerAltname.
# Import the email address.
# subjectAltName=email:copy
# An alternative to produce certificates that aren't
# deprecated according to PKIX.
# subjectAltName=email:move

# Copy subject details
# issuerAltName=issuer:copy

#nsCaRevocationUrl		= http://www.domain.dom/ca-crl.pem
#nsBaseUrl
#nsRevocationUrl
#nsRenewalUrl
#nsCaPolicyUrl
#nsSslServerName

# This really needs to be in place for it to be a proxy certificate.
proxyCertInfo=critical,language:id-ppl-anyLanguage,pathlen:3,policy:foo

####################################################################
[ tsa ]

default_tsa = tsa_config1	# the default TSA section

[ tsa_config1 ]

# These are used by the TSA reply generation only.
dir		= ./demoCA		# TSA root directory
serial		= \$dir/tsaserial	# The current serial number (mandatory)
crypto_device	= builtin		# OpenSSL engine to use for signing
signer_cert	= \$dir/tsacert.pem 	# The TSA signing certificate
					# (optional)
certs		= \$dir/cacert.pem	# Certificate chain to include in reply
					# (optional)
signer_key	= \$dir/private/tsakey.pem # The TSA private key (optional)

default_policy	= tsa_policy1		# Policy if request did not specify it
					# (optional)
other_policies	= tsa_policy2, tsa_policy3	# acceptable policies (optional)
digests		= md5, sha1		# Acceptable message digests (mandatory)
accuracy	= secs:1, millisecs:500, microsecs:100	# (optional)
clock_precision_digits  = 0	# number of digits after dot. (optional)
ordering		= yes	# Is ordering defined for timestamps?
				# (optional, default: no)
tsa_name		= yes	# Must the TSA name be included in the reply?
				# (optional, default: no)
ess_cert_id_chain	= no	# Must the ESS cert id chain be included?
				# (optional, default: no)

";
	
}





?>
--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.0
-- Dumped by pg_dump version 9.5.0

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: trimwhite(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION trimwhite(character varying) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$
declare
item alias for $1;
begin
return trim(regexp_replace(upper(item), '\s+', ' ', 'g'));
end;
$_$;


ALTER FUNCTION public.trimwhite(character varying) OWNER TO postgres;

--
-- Name: trimwhitenoup(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION trimwhitenoup(character varying) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$
declare
item alias for $1;
begin
return trim(regexp_replace(item, '\s+', ' ', 'g'));
end;
$_$;


ALTER FUNCTION public.trimwhitenoup(character varying) OWNER TO postgres;

--
-- Name: trip_status(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION trip_status(integer) RETURNS character varying[]
    LANGUAGE plpgsql
    AS $_$
declare
tripid alias for $1;
response varchar[] := '{}'; -- reposnse array to be sent back
rec record; -- user to store selection and loop through it
prevlocation integer ;
txtprevlocation varchar := '';
prevdate date;
prevaction varchar = '';
count integer := 0;
begin
	for rec in select tldaction, tldlocation, tldactiondate, locdescription || ' (' || rtrim(lttype, ' ') || ')' as loc, coalesce(locwarningdays, 2) as locwarningdays, coalesce(loctriplegtime,0) as loctriplegtime, tlbooked from 
	trip_log left join trip_log_det on tlid=tldtripid
	left join locations on tldlocation=locid 
	left join location_types on loctype=ltid
	where tldtripid=tripid order by tldid loop
		if rec.tldactiondate is null then
			-- First row always show at checkpoint
			if count = 0 then
				response[0] := rec.loc::varchar;
				response[1] := 'AWAITING AT YARD';
				response[2] := to_char(rec.tlbooked, 'dd/mm/YYYY')::varchar;
				response[3] := (now()::date - rec.tlbooked + 1)::varchar;
				response[4] := 'AT YARD';
				response[5] := rec.locwarningdays;
				return response;

			else
				if rec.tldlocation = prevlocation then
					-- We are at this location so give back location info, last action (offloading point loading point etc..) last date of action and time elasped from now
					response[0] := rec.loc::varchar;
					response[1] := prevaction::varchar;
					response[2] := to_char(prevdate, 'dd/mm/YYYY')::varchar;
					response[3] := (now()::date - prevdate + 1)::varchar;
					response[4] := 'AT CHECKPOINT';
					response[5] := rec.locwarningdays;
					--response := rec.loc || '§§§' || prevaction || '§§§' || to_char(prevdate, 'dd/mm/YYYY') || '§§§' || now()::date - prevdate;
					return response;
				else -- Different location so report the last know one
					--	response := txtprevlocation || '§§§' || prevaction || '§§§' || to_char(prevdate, 'dd/mm/YYYY') || '§§§' || now()::date - prevdate;
					response[0] := txtprevlocation;
					response[1] := prevaction::varchar;
					response[2] := to_char(prevdate, 'dd/mm/YYYY')::varchar;
					response[3] := (now()::date - prevdate + 1)::varchar;
					response[4] := 'ON THE ROAD';
					response[5] := rec.loctriplegtime;
					return response;
				end if;
			end if;
		end if;
		prevlocation := rec.tldlocation;
		txtprevlocation := rec.loc;
		prevdate := rec.tldactiondate;
		prevaction := rec.tldaction;
		count := count + 1;
	end loop;
	-- Nothing found, return 
	--return 'NO';
end;
$_$;


ALTER FUNCTION public.trip_status(integer) OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: backups; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE backups (
    bid integer NOT NULL,
    bdate timestamp without time zone DEFAULT now(),
    bfilename character varying,
    company_id integer
);


ALTER TABLE backups OWNER TO mtl;

--
-- Name: backups_bid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE backups_bid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE backups_bid_seq OWNER TO mtl;

--
-- Name: backups_bid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE backups_bid_seq OWNED BY backups.bid;


--
-- Name: countries; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE countries (
    cnid integer NOT NULL,
    cncountry character varying
);


ALTER TABLE countries OWNER TO mtl;

--
-- Name: countries_cnid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE countries_cnid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE countries_cnid_seq OWNER TO mtl;

--
-- Name: countries_cnid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE countries_cnid_seq OWNED BY countries.cnid;


--
-- Name: customer_emails; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE customer_emails (
    ceid integer NOT NULL,
    cecid integer,
    ceemail character varying
);


ALTER TABLE customer_emails OWNER TO mtl;

--
-- Name: customer_emails_ceid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE customer_emails_ceid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE customer_emails_ceid_seq OWNER TO mtl;

--
-- Name: customer_emails_ceid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE customer_emails_ceid_seq OWNED BY customer_emails.ceid;


--
-- Name: customer_phones; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE customer_phones (
    cpid integer NOT NULL,
    cpcid integer,
    cpphoneno character varying
);


ALTER TABLE customer_phones OWNER TO mtl;

--
-- Name: customer_phones_cpid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE customer_phones_cpid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE customer_phones_cpid_seq OWNER TO mtl;

--
-- Name: customer_phones_cpid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE customer_phones_cpid_seq OWNED BY customer_phones.cpid;


--
-- Name: customers; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE customers (
    cid integer NOT NULL,
    cname character varying,
    caddress text,
    cnumber character varying,
    cemail character varying,
    company_id integer
);


ALTER TABLE customers OWNER TO mtl;

--
-- Name: customers_cid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE customers_cid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE customers_cid_seq OWNER TO mtl;

--
-- Name: customers_cid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE customers_cid_seq OWNED BY customers.cid;


--
-- Name: driver_phones; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE driver_phones (
    dpid integer NOT NULL,
    dpdid integer,
    dpphoneno character varying
);


ALTER TABLE driver_phones OWNER TO mtl;

--
-- Name: driver_phones_dpid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE driver_phones_dpid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE driver_phones_dpid_seq OWNER TO mtl;

--
-- Name: driver_phones_dpid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE driver_phones_dpid_seq OWNED BY driver_phones.dpid;


--
-- Name: drivers; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE drivers (
    did integer NOT NULL,
    dname character varying NOT NULL,
    dlicenceno character varying(10) NOT NULL,
    dlicenseexp date,
    dpassportno character varying,
    dpassportexp date,
    company_id integer
);


ALTER TABLE drivers OWNER TO mtl;

--
-- Name: drivers_did_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE drivers_did_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE drivers_did_seq OWNER TO mtl;

--
-- Name: drivers_did_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE drivers_did_seq OWNED BY drivers.did;


--
-- Name: equipment; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE equipment (
    eqid integer NOT NULL,
    eqdescription character varying,
    company_id integer
);


ALTER TABLE equipment OWNER TO mtl;

--
-- Name: equipment_eqid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE equipment_eqid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE equipment_eqid_seq OWNER TO mtl;

--
-- Name: equipment_eqid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE equipment_eqid_seq OWNED BY equipment.eqid;


--
-- Name: equipment_sets; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE equipment_sets (
    eqsid integer NOT NULL,
    eqsdescription character varying,
    company_id integer
);


ALTER TABLE equipment_sets OWNER TO mtl;

--
-- Name: equipment_sets_det; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE equipment_sets_det (
    esdid integer NOT NULL,
    esdsetid integer,
    esditem integer,
    esdqty integer
);


ALTER TABLE equipment_sets_det OWNER TO mtl;

--
-- Name: equipment_sets_det_esdid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE equipment_sets_det_esdid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE equipment_sets_det_esdid_seq OWNER TO mtl;

--
-- Name: equipment_sets_det_esdid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE equipment_sets_det_esdid_seq OWNED BY equipment_sets_det.esdid;


--
-- Name: equipment_sets_eqsid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE equipment_sets_eqsid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE equipment_sets_eqsid_seq OWNER TO mtl;

--
-- Name: equipment_sets_eqsid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE equipment_sets_eqsid_seq OWNED BY equipment_sets.eqsid;


--
-- Name: expense_types; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE expense_types (
    etid integer NOT NULL,
    etdescription character varying,
    etfixedvalue numeric(12,2),
    etaverageperday boolean DEFAULT false NOT NULL,
    company_id integer
);


ALTER TABLE expense_types OWNER TO mtl;

--
-- Name: COLUMN expense_types.etfixedvalue; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN expense_types.etfixedvalue IS 'Fixed value. Used when creating a trip so that I can set a default value in the trip_expenses_log';


--
-- Name: COLUMN expense_types.etaverageperday; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN expense_types.etaverageperday IS 'Show average per day in reports';


--
-- Name: expense_types_etid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE expense_types_etid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE expense_types_etid_seq OWNER TO mtl;

--
-- Name: expense_types_etid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE expense_types_etid_seq OWNED BY expense_types.etid;


--
-- Name: location_types; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE location_types (
    ltid integer NOT NULL,
    lttype character varying,
    ltdeparturedate boolean DEFAULT false NOT NULL,
    ltarrivaldate boolean DEFAULT false NOT NULL,
    ltloadingdate boolean DEFAULT false NOT NULL,
    ltoffloadingdate boolean DEFAULT false NOT NULL,
    company_id integer
);


ALTER TABLE location_types OWNER TO mtl;

--
-- Name: location_types_ltid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE location_types_ltid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE location_types_ltid_seq OWNER TO mtl;

--
-- Name: location_types_ltid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE location_types_ltid_seq OWNED BY location_types.ltid;


--
-- Name: locations; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE locations (
    locid integer NOT NULL,
    locdescription character varying,
    loctype integer,
    locwarningdays integer,
    loctriplegtime integer,
    locdistance integer,
    company_id integer
);


ALTER TABLE locations OWNER TO mtl;

--
-- Name: COLUMN locations.loctriplegtime; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN locations.loctriplegtime IS 'This value is used to check how many days it takes to get to this location';


--
-- Name: COLUMN locations.locdistance; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN locations.locdistance IS 'KM to this place';


--
-- Name: locations_locid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE locations_locid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE locations_locid_seq OWNER TO mtl;

--
-- Name: locations_locid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE locations_locid_seq OWNED BY locations.locid;


--
-- Name: login; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE login (
    lid integer NOT NULL,
    lusername character varying,
    lpassword character varying,
    ldescription character varying,
    lcopypaste boolean DEFAULT false NOT NULL,
    lreadonly boolean DEFAULT false NOT NULL,
    lstataccess boolean DEFAULT false NOT NULL,
    ltripsteps boolean DEFAULT true NOT NULL,
    linvoicing boolean DEFAULT false NOT NULL,
    lemail character varying,
    ltripmod boolean DEFAULT false NOT NULL,
    company_id integer
);


ALTER TABLE login OWNER TO mtl;

--
-- Name: COLUMN login.ltripsteps; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN login.ltripsteps IS 'Set trip steps to be inserted one by one rather than all in one go';


--
-- Name: COLUMN login.lemail; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN login.lemail IS 'Users email address';


--
-- Name: COLUMN login.ltripmod; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN login.ltripmod IS 'Can user modify trip after it being closed?';


--
-- Name: login_lid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE login_lid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE login_lid_seq OWNER TO mtl;

--
-- Name: login_lid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE login_lid_seq OWNED BY login.lid;


--
-- Name: login_permissions; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE login_permissions (
    lpid integer NOT NULL,
    lpuser integer,
    lpperm integer
);


ALTER TABLE login_permissions OWNER TO mtl;

--
-- Name: login_permissions_lpid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE login_permissions_lpid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE login_permissions_lpid_seq OWNER TO mtl;

--
-- Name: login_permissions_lpid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE login_permissions_lpid_seq OWNED BY login_permissions.lpid;


--
-- Name: menu; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE menu (
    mid integer NOT NULL,
    mpage character varying,
    mtitle character varying,
    mdescription character varying,
    morder integer,
    mspacebefore boolean DEFAULT false NOT NULL,
    micon character varying
);


ALTER TABLE menu OWNER TO mtl;

--
-- Name: COLUMN menu.morder; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN menu.morder IS 'Ordering in main page';


--
-- Name: COLUMN menu.mspacebefore; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN menu.mspacebefore IS 'add a line break before item';


--
-- Name: menu_mid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE menu_mid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE menu_mid_seq OWNER TO mtl;

--
-- Name: menu_mid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE menu_mid_seq OWNED BY menu.mid;


--
-- Name: quotation_permits; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE quotation_permits (
    qpid integer NOT NULL,
    qpdescription character varying
);


ALTER TABLE quotation_permits OWNER TO mtl;

--
-- Name: quotation_permits_qpid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE quotation_permits_qpid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE quotation_permits_qpid_seq OWNER TO mtl;

--
-- Name: quotation_permits_qpid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE quotation_permits_qpid_seq OWNED BY quotation_permits.qpid;


--
-- Name: quotations; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE quotations (
    qid integer NOT NULL,
    qdate date DEFAULT now() NOT NULL,
    qclient integer,
    qdestination character varying,
    qcargo character varying,
    qlength numeric(4,2),
    qwidth numeric(4,2),
    qheight numeric(4,2),
    qweight numeric(8,2),
    qvalue integer,
    qpermits integer,
    qnotes text,
    qcountry integer,
    qnotesinternal text,
    company_id integer
);


ALTER TABLE quotations OWNER TO mtl;

--
-- Name: quotations_qid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE quotations_qid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE quotations_qid_seq OWNER TO mtl;

--
-- Name: quotations_qid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE quotations_qid_seq OWNED BY quotations.qid;


--
-- Name: trailers; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE trailers (
    trid integer NOT NULL,
    trnumberplate character varying(7),
    trchassisnumber character varying,
    trmake character varying,
    tryear integer,
    traxles integer,
    trroadlicense date,
    company_id integer,
    CONSTRAINT axle_number CHECK ((traxles <= 5))
);


ALTER TABLE trailers OWNER TO mtl;

--
-- Name: COLUMN trailers.traxles; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN trailers.traxles IS 'NUmber of axles on the trailer';


--
-- Name: CONSTRAINT axle_number ON trailers; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON CONSTRAINT axle_number ON trailers IS 'truck has maximum 5 axles';


--
-- Name: trailers_trid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE trailers_trid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE trailers_trid_seq OWNER TO mtl;

--
-- Name: trailers_trid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE trailers_trid_seq OWNED BY trailers.trid;


--
-- Name: trip_config; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE trip_config (
    tcid integer NOT NULL,
    tcdescription character varying,
    tcdistance integer,
    company_id integer
);


ALTER TABLE trip_config OWNER TO mtl;

--
-- Name: COLUMN trip_config.tcdistance; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN trip_config.tcdistance IS 'KM of trip';


--
-- Name: trip_config_det; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE trip_config_det (
    tcdid integer NOT NULL,
    tcdtripid integer,
    tcdlocation integer
);


ALTER TABLE trip_config_det OWNER TO mtl;

--
-- Name: trip_config_det_tcdid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE trip_config_det_tcdid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE trip_config_det_tcdid_seq OWNER TO mtl;

--
-- Name: trip_config_det_tcdid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE trip_config_det_tcdid_seq OWNED BY trip_config_det.tcdid;


--
-- Name: trip_config_expenses; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE trip_config_expenses (
    tceid integer NOT NULL,
    tcetripid integer,
    tceexpense integer,
    tcefixedvalue numeric(12,2)
);


ALTER TABLE trip_config_expenses OWNER TO mtl;

--
-- Name: trip_config_fixed_expenses_tceid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE trip_config_fixed_expenses_tceid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE trip_config_fixed_expenses_tceid_seq OWNER TO mtl;

--
-- Name: trip_config_fixed_expenses_tceid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE trip_config_fixed_expenses_tceid_seq OWNED BY trip_config_expenses.tceid;


--
-- Name: trip_config_tcid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE trip_config_tcid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE trip_config_tcid_seq OWNER TO mtl;

--
-- Name: trip_config_tcid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE trip_config_tcid_seq OWNED BY trip_config.tcid;


--
-- Name: trip_log; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE trip_log (
    tlid integer NOT NULL,
    tltruck integer,
    tldriver integer,
    tltripconfig integer,
    tlcustomer1 integer,
    tlcustomer2 integer,
    tlbooked date DEFAULT now(),
    tloperator integer,
    tlclosed boolean DEFAULT false NOT NULL,
    tltrailer integer,
    tlcontainer character varying(11),
    tlequipment integer,
    tlvalue numeric(12,2),
    tlvalueret numeric(12,2),
    tlinvoiceno character varying,
    tlinvoicedate date,
    tlcontainer_ret character varying(11),
    tlcargo text,
    tlcargo_ret text,
    tlinvoiceno_ret character varying,
    tlinvoicedate_ret date,
    company_id integer
);


ALTER TABLE trip_log OWNER TO mtl;

--
-- Name: COLUMN trip_log.tltrailer; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN trip_log.tltrailer IS 'Trailer for trip';


--
-- Name: COLUMN trip_log.tlequipment; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN trip_log.tlequipment IS 'What equipment has this driver taken for this trip?';


--
-- Name: COLUMN trip_log.tlvalue; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN trip_log.tlvalue IS 'How much is the trip paid';


--
-- Name: COLUMN trip_log.tlvalueret; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN trip_log.tlvalueret IS 'Value of trip on return leg';


--
-- Name: COLUMN trip_log.tlcargo; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN trip_log.tlcargo IS 'cargo going';


--
-- Name: COLUMN trip_log.tlcargo_ret; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN trip_log.tlcargo_ret IS 'Cargo returning';


--
-- Name: trip_log_det; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE trip_log_det (
    tldid integer NOT NULL,
    tldtripid integer,
    tldlocation integer,
    tldaction character varying,
    tldactiondate date,
    tldorder integer
);


ALTER TABLE trip_log_det OWNER TO mtl;

--
-- Name: COLUMN trip_log_det.tldorder; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN trip_log_det.tldorder IS 'Ordering for arrival loading offloading and departure actions so user does not get confused!';


--
-- Name: trip_log_det_tldid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE trip_log_det_tldid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE trip_log_det_tldid_seq OWNER TO mtl;

--
-- Name: trip_log_det_tldid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE trip_log_det_tldid_seq OWNED BY trip_log_det.tldid;


--
-- Name: trip_log_expenses; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE trip_log_expenses (
    tleid integer NOT NULL,
    tletripid integer,
    tleetid integer,
    tlevalue numeric(12,2),
    tledate date DEFAULT now() NOT NULL,
    tlecomment text,
    tlelocation integer
);


ALTER TABLE trip_log_expenses OWNER TO mtl;

--
-- Name: trip_log_expenses_tleid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE trip_log_expenses_tleid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE trip_log_expenses_tleid_seq OWNER TO mtl;

--
-- Name: trip_log_expenses_tleid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE trip_log_expenses_tleid_seq OWNED BY trip_log_expenses.tleid;


--
-- Name: trip_log_fuel; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE trip_log_fuel (
    tlfid integer NOT NULL,
    tlftripid integer,
    tlfdate date DEFAULT now(),
    tlfqty integer,
    tlfvalue integer,
    tlflocation integer
);


ALTER TABLE trip_log_fuel OWNER TO mtl;

--
-- Name: trip_log_fuel_tlfid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE trip_log_fuel_tlfid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE trip_log_fuel_tlfid_seq OWNER TO mtl;

--
-- Name: trip_log_fuel_tlfid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE trip_log_fuel_tlfid_seq OWNED BY trip_log_fuel.tlfid;


--
-- Name: trip_log_tlid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE trip_log_tlid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE trip_log_tlid_seq OWNER TO mtl;

--
-- Name: trip_log_tlid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE trip_log_tlid_seq OWNED BY trip_log.tlid;


--
-- Name: trucks; Type: TABLE; Schema: public; Owner: mtl
--

CREATE TABLE trucks (
    tid integer NOT NULL,
    tnumberplate character varying(7),
    tenginenumber character varying,
    tchassisnumber character varying,
    ttrailer integer,
    tmake character varying,
    tyear integer,
    troadlicense date,
    company_id integer
);


ALTER TABLE trucks OWNER TO mtl;

--
-- Name: COLUMN trucks.ttrailer; Type: COMMENT; Schema: public; Owner: mtl
--

COMMENT ON COLUMN trucks.ttrailer IS 'which trailer is this truck using?';


--
-- Name: trucks_tid_seq; Type: SEQUENCE; Schema: public; Owner: mtl
--

CREATE SEQUENCE trucks_tid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE trucks_tid_seq OWNER TO mtl;

--
-- Name: trucks_tid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mtl
--

ALTER SEQUENCE trucks_tid_seq OWNED BY trucks.tid;


--
-- Name: bid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY backups ALTER COLUMN bid SET DEFAULT nextval('backups_bid_seq'::regclass);


--
-- Name: cnid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY countries ALTER COLUMN cnid SET DEFAULT nextval('countries_cnid_seq'::regclass);


--
-- Name: ceid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY customer_emails ALTER COLUMN ceid SET DEFAULT nextval('customer_emails_ceid_seq'::regclass);


--
-- Name: cpid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY customer_phones ALTER COLUMN cpid SET DEFAULT nextval('customer_phones_cpid_seq'::regclass);


--
-- Name: cid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY customers ALTER COLUMN cid SET DEFAULT nextval('customers_cid_seq'::regclass);


--
-- Name: dpid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY driver_phones ALTER COLUMN dpid SET DEFAULT nextval('driver_phones_dpid_seq'::regclass);


--
-- Name: did; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY drivers ALTER COLUMN did SET DEFAULT nextval('drivers_did_seq'::regclass);


--
-- Name: eqid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY equipment ALTER COLUMN eqid SET DEFAULT nextval('equipment_eqid_seq'::regclass);


--
-- Name: eqsid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY equipment_sets ALTER COLUMN eqsid SET DEFAULT nextval('equipment_sets_eqsid_seq'::regclass);


--
-- Name: esdid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY equipment_sets_det ALTER COLUMN esdid SET DEFAULT nextval('equipment_sets_det_esdid_seq'::regclass);


--
-- Name: etid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY expense_types ALTER COLUMN etid SET DEFAULT nextval('expense_types_etid_seq'::regclass);


--
-- Name: ltid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY location_types ALTER COLUMN ltid SET DEFAULT nextval('location_types_ltid_seq'::regclass);


--
-- Name: locid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY locations ALTER COLUMN locid SET DEFAULT nextval('locations_locid_seq'::regclass);


--
-- Name: lid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY login ALTER COLUMN lid SET DEFAULT nextval('login_lid_seq'::regclass);


--
-- Name: lpid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY login_permissions ALTER COLUMN lpid SET DEFAULT nextval('login_permissions_lpid_seq'::regclass);


--
-- Name: mid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY menu ALTER COLUMN mid SET DEFAULT nextval('menu_mid_seq'::regclass);


--
-- Name: qpid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY quotation_permits ALTER COLUMN qpid SET DEFAULT nextval('quotation_permits_qpid_seq'::regclass);


--
-- Name: qid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY quotations ALTER COLUMN qid SET DEFAULT nextval('quotations_qid_seq'::regclass);


--
-- Name: trid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trailers ALTER COLUMN trid SET DEFAULT nextval('trailers_trid_seq'::regclass);


--
-- Name: tcid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_config ALTER COLUMN tcid SET DEFAULT nextval('trip_config_tcid_seq'::regclass);


--
-- Name: tcdid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_config_det ALTER COLUMN tcdid SET DEFAULT nextval('trip_config_det_tcdid_seq'::regclass);


--
-- Name: tceid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_config_expenses ALTER COLUMN tceid SET DEFAULT nextval('trip_config_fixed_expenses_tceid_seq'::regclass);


--
-- Name: tlid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log ALTER COLUMN tlid SET DEFAULT nextval('trip_log_tlid_seq'::regclass);


--
-- Name: tldid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_det ALTER COLUMN tldid SET DEFAULT nextval('trip_log_det_tldid_seq'::regclass);


--
-- Name: tleid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_expenses ALTER COLUMN tleid SET DEFAULT nextval('trip_log_expenses_tleid_seq'::regclass);


--
-- Name: tlfid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_fuel ALTER COLUMN tlfid SET DEFAULT nextval('trip_log_fuel_tlfid_seq'::regclass);


--
-- Name: tid; Type: DEFAULT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trucks ALTER COLUMN tid SET DEFAULT nextval('trucks_tid_seq'::regclass);


--
-- Name: backups_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY backups
    ADD CONSTRAINT backups_pkey PRIMARY KEY (bid);


--
-- Name: cname_unique; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY customers
    ADD CONSTRAINT cname_unique UNIQUE (cname);


--
-- Name: countries_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY countries
    ADD CONSTRAINT countries_pkey PRIMARY KEY (cnid);


--
-- Name: country_name; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY countries
    ADD CONSTRAINT country_name UNIQUE (cncountry);


--
-- Name: customer_emails_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY customer_emails
    ADD CONSTRAINT customer_emails_pkey PRIMARY KEY (ceid);


--
-- Name: customer_phones_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY customer_phones
    ADD CONSTRAINT customer_phones_pkey PRIMARY KEY (cpid);


--
-- Name: customers_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY customers
    ADD CONSTRAINT customers_pkey PRIMARY KEY (cid);


--
-- Name: dlicenseno_unique; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY drivers
    ADD CONSTRAINT dlicenseno_unique UNIQUE (dlicenceno);


--
-- Name: dname_unique; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY drivers
    ADD CONSTRAINT dname_unique UNIQUE (dname);


--
-- Name: dpassportno_unique; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY drivers
    ADD CONSTRAINT dpassportno_unique UNIQUE (dpassportno);


--
-- Name: driver_phones_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY driver_phones
    ADD CONSTRAINT driver_phones_pkey PRIMARY KEY (dpid);


--
-- Name: drivers_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY drivers
    ADD CONSTRAINT drivers_pkey PRIMARY KEY (did);


--
-- Name: eqdescription_unique; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY equipment
    ADD CONSTRAINT eqdescription_unique UNIQUE (eqdescription);


--
-- Name: eqsdescription_unique; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY equipment_sets
    ADD CONSTRAINT eqsdescription_unique UNIQUE (eqsdescription);


--
-- Name: equipment_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY equipment
    ADD CONSTRAINT equipment_pkey PRIMARY KEY (eqid);


--
-- Name: equipment_sets_det_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY equipment_sets_det
    ADD CONSTRAINT equipment_sets_det_pkey PRIMARY KEY (esdid);


--
-- Name: equipment_sets_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY equipment_sets
    ADD CONSTRAINT equipment_sets_pkey PRIMARY KEY (eqsid);


--
-- Name: etdescription_unique; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY expense_types
    ADD CONSTRAINT etdescription_unique UNIQUE (etdescription);


--
-- Name: expense_types_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY expense_types
    ADD CONSTRAINT expense_types_pkey PRIMARY KEY (etid);


--
-- Name: location_types_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY location_types
    ADD CONSTRAINT location_types_pkey PRIMARY KEY (ltid);


--
-- Name: locations_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT locations_pkey PRIMARY KEY (locid);


--
-- Name: login_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY login_permissions
    ADD CONSTRAINT login_permissions_pkey PRIMARY KEY (lpid);


--
-- Name: login_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY login
    ADD CONSTRAINT login_pkey PRIMARY KEY (lid);


--
-- Name: lttype_unique; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY location_types
    ADD CONSTRAINT lttype_unique UNIQUE (lttype);


--
-- Name: lusername_unique; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY login
    ADD CONSTRAINT lusername_unique UNIQUE (lusername);


--
-- Name: menu_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY menu
    ADD CONSTRAINT menu_pkey PRIMARY KEY (mid);


--
-- Name: qpdescription; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY quotation_permits
    ADD CONSTRAINT qpdescription UNIQUE (qpdescription);


--
-- Name: quotation_permits_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY quotation_permits
    ADD CONSTRAINT quotation_permits_pkey PRIMARY KEY (qpid);


--
-- Name: quotations_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY quotations
    ADD CONSTRAINT quotations_pkey PRIMARY KEY (qid);


--
-- Name: tcdescription_uniqie; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_config
    ADD CONSTRAINT tcdescription_uniqie UNIQUE (tcdescription);


--
-- Name: tnumberplate_unique; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trucks
    ADD CONSTRAINT tnumberplate_unique UNIQUE (tnumberplate);


--
-- Name: trailers_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trailers
    ADD CONSTRAINT trailers_pkey PRIMARY KEY (trid);


--
-- Name: trip_config_det_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_config_det
    ADD CONSTRAINT trip_config_det_pkey PRIMARY KEY (tcdid);


--
-- Name: trip_config_fixed_expenses_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_config_expenses
    ADD CONSTRAINT trip_config_fixed_expenses_pkey PRIMARY KEY (tceid);


--
-- Name: trip_config_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_config
    ADD CONSTRAINT trip_config_pkey PRIMARY KEY (tcid);


--
-- Name: trip_lod_det_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_det
    ADD CONSTRAINT trip_lod_det_pkey PRIMARY KEY (tldid);


--
-- Name: trip_log_expenses_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_expenses
    ADD CONSTRAINT trip_log_expenses_pkey PRIMARY KEY (tleid);


--
-- Name: trip_log_fuel_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_fuel
    ADD CONSTRAINT trip_log_fuel_pkey PRIMARY KEY (tlfid);


--
-- Name: trip_log_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log
    ADD CONSTRAINT trip_log_pkey PRIMARY KEY (tlid);


--
-- Name: trnumberplate_unique; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trailers
    ADD CONSTRAINT trnumberplate_unique UNIQUE (trnumberplate);


--
-- Name: trucks_pkey; Type: CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trucks
    ADD CONSTRAINT trucks_pkey PRIMARY KEY (tid);


--
-- Name: equipment_sets_det_esdsetid_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX equipment_sets_det_esdsetid_idx ON equipment_sets_det USING btree (esdsetid);


--
-- Name: fki_fkey_trailer; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX fki_fkey_trailer ON trucks USING btree (ttrailer);


--
-- Name: fki_loctype->lttype; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX "fki_loctype->lttype" ON locations USING btree (loctype);


--
-- Name: fki_quotations_qcountry -> countries; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX "fki_quotations_qcountry -> countries" ON quotations USING btree (qcountry);


--
-- Name: trip_config_det_tcdtripid_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_config_det_tcdtripid_idx ON trip_config_det USING btree (tcdtripid);


--
-- Name: trip_config_expenses_tceexpense_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_config_expenses_tceexpense_idx ON trip_config_expenses USING btree (tceexpense);


--
-- Name: trip_config_expenses_tcetripid_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_config_expenses_tcetripid_idx ON trip_config_expenses USING btree (tcetripid);


--
-- Name: trip_lod_det->tripid-tldtlid; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX "trip_lod_det->tripid-tldtlid" ON trip_log_det USING btree (tldtripid);


--
-- Name: trip_log_det->tldactiondate; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX "trip_log_det->tldactiondate" ON trip_log_det USING btree (tldactiondate);


--
-- Name: trip_log_det->tldlocation; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX "trip_log_det->tldlocation" ON trip_log_det USING btree (tldlocation);


--
-- Name: trip_log_det_tldtripid; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_det_tldtripid ON trip_log_det USING btree (tldtripid);


--
-- Name: trip_log_expenses_tleetid_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_expenses_tleetid_idx ON trip_log_expenses USING btree (tleetid);


--
-- Name: trip_log_expenses_tlelocation_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_expenses_tlelocation_idx ON trip_log_expenses USING btree (tlelocation);


--
-- Name: trip_log_expenses_tletripid_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_expenses_tletripid_idx ON trip_log_expenses USING btree (tletripid);


--
-- Name: trip_log_fuel_tlflocation_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_fuel_tlflocation_idx ON trip_log_fuel USING btree (tlflocation);


--
-- Name: trip_log_fuel_tlftripid_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_fuel_tlftripid_idx ON trip_log_fuel USING btree (tlftripid);


--
-- Name: trip_log_tlclosed_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_tlclosed_idx ON trip_log USING btree (tlclosed);


--
-- Name: trip_log_tlcontainer_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_tlcontainer_idx ON trip_log USING btree (tlcontainer);


--
-- Name: trip_log_tlcustomer1_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_tlcustomer1_idx ON trip_log USING btree (tlcustomer1);


--
-- Name: trip_log_tlcustomer2_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_tlcustomer2_idx ON trip_log USING btree (tlcustomer2);


--
-- Name: trip_log_tldriver_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_tldriver_idx ON trip_log USING btree (tldriver);


--
-- Name: trip_log_tlequipment_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_tlequipment_idx ON trip_log USING btree (tlequipment);


--
-- Name: trip_log_tltrailer_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_tltrailer_idx ON trip_log USING btree (tltrailer);


--
-- Name: trip_log_tltripconfig_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_tltripconfig_idx ON trip_log USING btree (tltripconfig);


--
-- Name: trip_log_tltruck_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trip_log_tltruck_idx ON trip_log USING btree (tltruck);


--
-- Name: trucks_ttrailer_idx; Type: INDEX; Schema: public; Owner: mtl
--

CREATE INDEX trucks_ttrailer_idx ON trucks USING btree (ttrailer);


--
-- Name: customer_emails_cecid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY customer_emails
    ADD CONSTRAINT customer_emails_cecid_fkey FOREIGN KEY (cecid) REFERENCES customers(cid) ON DELETE CASCADE;


--
-- Name: customer_phones_cpcid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY customer_phones
    ADD CONSTRAINT customer_phones_cpcid_fkey FOREIGN KEY (cpcid) REFERENCES customers(cid) ON DELETE CASCADE;


--
-- Name: driver_phones_dpdid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY driver_phones
    ADD CONSTRAINT driver_phones_dpdid_fkey FOREIGN KEY (dpdid) REFERENCES drivers(did) ON DELETE CASCADE;


--
-- Name: equipment_sets_det_esditem_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY equipment_sets_det
    ADD CONSTRAINT equipment_sets_det_esditem_fkey FOREIGN KEY (esditem) REFERENCES equipment(eqid);


--
-- Name: equipment_sets_det_esdsetid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY equipment_sets_det
    ADD CONSTRAINT equipment_sets_det_esdsetid_fkey FOREIGN KEY (esdsetid) REFERENCES equipment_sets(eqsid) ON DELETE CASCADE;


--
-- Name: fkey_trailer; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trucks
    ADD CONSTRAINT fkey_trailer FOREIGN KEY (ttrailer) REFERENCES trailers(trid);


--
-- Name: loctype->lttype; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT "loctype->lttype" FOREIGN KEY (loctype) REFERENCES location_types(ltid);


--
-- Name: login_permissions_lpperm_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY login_permissions
    ADD CONSTRAINT login_permissions_lpperm_fkey FOREIGN KEY (lpperm) REFERENCES menu(mid);


--
-- Name: login_permissions_lpuser_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY login_permissions
    ADD CONSTRAINT login_permissions_lpuser_fkey FOREIGN KEY (lpuser) REFERENCES login(lid);


--
-- Name: quotations_qclient_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY quotations
    ADD CONSTRAINT quotations_qclient_fkey FOREIGN KEY (qclient) REFERENCES customers(cid);


--
-- Name: quotations_qcountry -> countries; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY quotations
    ADD CONSTRAINT "quotations_qcountry -> countries" FOREIGN KEY (qcountry) REFERENCES countries(cnid);


--
-- Name: quotations_qpermits_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY quotations
    ADD CONSTRAINT quotations_qpermits_fkey FOREIGN KEY (qpermits) REFERENCES quotation_permits(qpid);


--
-- Name: trip_config_det_tcdlocation_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_config_det
    ADD CONSTRAINT trip_config_det_tcdlocation_fkey FOREIGN KEY (tcdlocation) REFERENCES locations(locid);


--
-- Name: trip_config_det_tcdtripid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_config_det
    ADD CONSTRAINT trip_config_det_tcdtripid_fkey FOREIGN KEY (tcdtripid) REFERENCES trip_config(tcid) ON DELETE CASCADE;


--
-- Name: trip_config_fixed_expenses_tceexpense_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_config_expenses
    ADD CONSTRAINT trip_config_fixed_expenses_tceexpense_fkey FOREIGN KEY (tceexpense) REFERENCES expense_types(etid);


--
-- Name: trip_config_fixed_expenses_tcetripid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_config_expenses
    ADD CONSTRAINT trip_config_fixed_expenses_tcetripid_fkey FOREIGN KEY (tcetripid) REFERENCES trip_config(tcid) ON DELETE CASCADE;


--
-- Name: trip_lod_det_tldlocation_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_det
    ADD CONSTRAINT trip_lod_det_tldlocation_fkey FOREIGN KEY (tldlocation) REFERENCES locations(locid);


--
-- Name: trip_lod_det_tldtlid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_det
    ADD CONSTRAINT trip_lod_det_tldtlid_fkey FOREIGN KEY (tldtripid) REFERENCES trip_log(tlid) ON DELETE CASCADE;


--
-- Name: trip_log_expenses_tleetid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_expenses
    ADD CONSTRAINT trip_log_expenses_tleetid_fkey FOREIGN KEY (tleetid) REFERENCES expense_types(etid);


--
-- Name: trip_log_expenses_tlelocation_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_expenses
    ADD CONSTRAINT trip_log_expenses_tlelocation_fkey FOREIGN KEY (tlelocation) REFERENCES locations(locid);


--
-- Name: trip_log_expenses_tletripid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_expenses
    ADD CONSTRAINT trip_log_expenses_tletripid_fkey FOREIGN KEY (tletripid) REFERENCES trip_log(tlid) ON DELETE CASCADE;


--
-- Name: trip_log_fuel_tlflocation_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_fuel
    ADD CONSTRAINT trip_log_fuel_tlflocation_fkey FOREIGN KEY (tlflocation) REFERENCES locations(locid);


--
-- Name: trip_log_fuel_tlftripid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log_fuel
    ADD CONSTRAINT trip_log_fuel_tlftripid_fkey FOREIGN KEY (tlftripid) REFERENCES trip_log(tlid) ON DELETE CASCADE;


--
-- Name: trip_log_tlcustomer1_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log
    ADD CONSTRAINT trip_log_tlcustomer1_fkey FOREIGN KEY (tlcustomer1) REFERENCES customers(cid);


--
-- Name: trip_log_tlcustomer2_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log
    ADD CONSTRAINT trip_log_tlcustomer2_fkey FOREIGN KEY (tlcustomer2) REFERENCES customers(cid);


--
-- Name: trip_log_tldriver_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log
    ADD CONSTRAINT trip_log_tldriver_fkey FOREIGN KEY (tldriver) REFERENCES drivers(did);


--
-- Name: trip_log_tlequipment_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log
    ADD CONSTRAINT trip_log_tlequipment_fkey FOREIGN KEY (tlequipment) REFERENCES equipment_sets(eqsid);


--
-- Name: trip_log_tloperator_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log
    ADD CONSTRAINT trip_log_tloperator_fkey FOREIGN KEY (tloperator) REFERENCES login(lid);


--
-- Name: trip_log_tltrailer_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log
    ADD CONSTRAINT trip_log_tltrailer_fkey FOREIGN KEY (tltrailer) REFERENCES trailers(trid);


--
-- Name: trip_log_tltripconfig_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log
    ADD CONSTRAINT trip_log_tltripconfig_fkey FOREIGN KEY (tltripconfig) REFERENCES trip_config(tcid);


--
-- Name: trip_log_tltruck_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mtl
--

ALTER TABLE ONLY trip_log
    ADD CONSTRAINT trip_log_tltruck_fkey FOREIGN KEY (tltruck) REFERENCES trucks(tid);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--


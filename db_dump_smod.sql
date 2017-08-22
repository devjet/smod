--
-- PostgreSQL database dump
--

-- Dumped from database version 9.4.8
-- Dumped by pg_dump version 9.4.8
-- Started on 2017-01-17 18:17:46

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 1 (class 3079 OID 11855)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 2137 (class 0 OID 0)
-- Dependencies: 1
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- TOC entry 2 (class 3079 OID 1092259)
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- TOC entry 2138 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


--
-- TOC entry 3 (class 3079 OID 1092248)
-- Name: uuid-ossp; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA public;


--
-- TOC entry 2139 (class 0 OID 0)
-- Dependencies: 3
-- Name: EXTENSION "uuid-ossp"; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION "uuid-ossp" IS 'generate universally unique identifiers (UUIDs)';


SET search_path = public, pg_catalog;

SET default_with_oids = false;

--
-- TOC entry 178 (class 1259 OID 1092307)
-- Name: attachment; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE attachment (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    type text NOT NULL,
    path text,
    filename text,
    size integer
);


--
-- TOC entry 184 (class 1259 OID 1100460)
-- Name: conversation; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE conversation (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    begin_at timestamp without time zone DEFAULT now(),
    last_message_id uuid,
    message_group_id uuid
);


--
-- TOC entry 183 (class 1259 OID 1092381)
-- Name: device; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE device (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    handle text
);


--
-- TOC entry 182 (class 1259 OID 1092367)
-- Name: member; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE member (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    phone text
);


--
-- TOC entry 2140 (class 0 OID 0)
-- Dependencies: 182
-- Name: COLUMN member.phone; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN member.phone IS 'Here we can reffer to
table member where actual phone will be placed';


--
-- TOC entry 185 (class 1259 OID 1100503)
-- Name: member_device; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE member_device (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    device_id uuid,
    member_id uuid
);


--
-- TOC entry 177 (class 1259 OID 1092297)
-- Name: message; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE message (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    "timestamp" timestamp without time zone DEFAULT now() NOT NULL,
    sender_id uuid,
    text text NOT NULL,
    receiver_id uuid,
    service_id integer,
    group_id uuid NOT NULL,
    device_id uuid NOT NULL,
    conversation_id uuid NOT NULL,
    is_attachment smallint DEFAULT 0 NOT NULL
);


--
-- TOC entry 2141 (class 0 OID 0)
-- Dependencies: 177
-- Name: COLUMN message.sender_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN message.sender_id IS 'from_me';


--
-- TOC entry 2142 (class 0 OID 0)
-- Dependencies: 177
-- Name: COLUMN message.receiver_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN message.receiver_id IS 'handle';


--
-- TOC entry 181 (class 1259 OID 1092334)
-- Name: message_attachment; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE message_attachment (
    attachment_id uuid,
    message_id uuid NOT NULL,
    id uuid DEFAULT gen_random_uuid() NOT NULL
);


--
-- TOC entry 179 (class 1259 OID 1092316)
-- Name: message_group; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE message_group (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    name text
);


--
-- TOC entry 180 (class 1259 OID 1092325)
-- Name: message_group_member; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE message_group_member (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    group_id uuid,
    member_id uuid
);


--
-- TOC entry 176 (class 1259 OID 1092238)
-- Name: message_service; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE message_service (
    id integer NOT NULL,
    name text NOT NULL
);


--
-- TOC entry 175 (class 1259 OID 1092236)
-- Name: message_service_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE message_service_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2143 (class 0 OID 0)
-- Dependencies: 175
-- Name: message_service_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE message_service_id_seq OWNED BY message_service.id;


--
-- TOC entry 1969 (class 2604 OID 1092241)
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY message_service ALTER COLUMN id SET DEFAULT nextval('message_service_id_seq'::regclass);


--
-- TOC entry 1991 (class 2606 OID 1092314)
-- Name: attachment_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY attachment
    ADD CONSTRAINT attachment_pkey PRIMARY KEY (id);


--
-- TOC entry 2010 (class 2606 OID 1100465)
-- Name: conversation_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY conversation
    ADD CONSTRAINT conversation_pkey PRIMARY KEY (id);


--
-- TOC entry 2007 (class 2606 OID 1092388)
-- Name: device_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY device
    ADD CONSTRAINT device_pkey PRIMARY KEY (id);


--
-- TOC entry 2012 (class 2606 OID 1100507)
-- Name: member_device_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY member_device
    ADD CONSTRAINT member_device_pkey PRIMARY KEY (id);


--
-- TOC entry 2001 (class 2606 OID 1101046)
-- Name: message_attachment_id_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY message_attachment
    ADD CONSTRAINT message_attachment_id_pk PRIMARY KEY (id);


--
-- TOC entry 1999 (class 2606 OID 1092329)
-- Name: message_group_member_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY message_group_member
    ADD CONSTRAINT message_group_member_pkey PRIMARY KEY (id);


--
-- TOC entry 1994 (class 2606 OID 1092323)
-- Name: message_group_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY message_group
    ADD CONSTRAINT message_group_pkey PRIMARY KEY (id);


--
-- TOC entry 2004 (class 2606 OID 1092374)
-- Name: message_member_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY member
    ADD CONSTRAINT message_member_pkey PRIMARY KEY (id);


--
-- TOC entry 1988 (class 2606 OID 1092305)
-- Name: message_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY message
    ADD CONSTRAINT message_pkey PRIMARY KEY (id);


--
-- TOC entry 1984 (class 2606 OID 1092246)
-- Name: message_service_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY message_service
    ADD CONSTRAINT message_service_pkey PRIMARY KEY (id);


--
-- TOC entry 1989 (class 1259 OID 1092315)
-- Name: attachment_id_uindex; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX attachment_id_uindex ON attachment USING btree (id);


--
-- TOC entry 2008 (class 1259 OID 1100466)
-- Name: conversation_begin_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX conversation_begin_at_index ON conversation USING btree (begin_at);


--
-- TOC entry 2005 (class 1259 OID 1092389)
-- Name: device_handle_uindex; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX device_handle_uindex ON device USING btree (handle);


--
-- TOC entry 1985 (class 1259 OID 1100467)
-- Name: message_conversation_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX message_conversation_id_index ON message USING btree (conversation_id);


--
-- TOC entry 1992 (class 1259 OID 1092324)
-- Name: message_group_id_uindex; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX message_group_id_uindex ON message_group USING btree (id);


--
-- TOC entry 1995 (class 1259 OID 1092361)
-- Name: message_group_member_group_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX message_group_member_group_id_index ON message_group_member USING btree (group_id);


--
-- TOC entry 1996 (class 1259 OID 1092330)
-- Name: message_group_member_id_uindex; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX message_group_member_id_uindex ON message_group_member USING btree (id);


--
-- TOC entry 1997 (class 1259 OID 1092331)
-- Name: message_group_member_member_id_group_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX message_group_member_member_id_group_id_index ON message_group_member USING btree (member_id, group_id);


--
-- TOC entry 1986 (class 1259 OID 1092306)
-- Name: message_id_uindex; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX message_id_uindex ON message USING btree (id);


--
-- TOC entry 2002 (class 1259 OID 1092375)
-- Name: message_member_phone_uindex; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX message_member_phone_uindex ON member USING btree (phone);


--
-- TOC entry 1982 (class 1259 OID 1092247)
-- Name: message_service_name_uindex; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX message_service_name_uindex ON message_service USING btree (name);


--
-- TOC entry 2019 (class 2606 OID 1100907)
-- Name: conversation_message_group_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY conversation
    ADD CONSTRAINT conversation_message_group_id_fk FOREIGN KEY (message_group_id) REFERENCES message_group(id);


--
-- TOC entry 2021 (class 2606 OID 1100513)
-- Name: member_device_device_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY member_device
    ADD CONSTRAINT member_device_device_id_fk FOREIGN KEY (device_id) REFERENCES device(id);


--
-- TOC entry 2020 (class 2606 OID 1100508)
-- Name: member_device_member_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY member_device
    ADD CONSTRAINT member_device_member_id_fk FOREIGN KEY (member_id) REFERENCES member(id);


--
-- TOC entry 2017 (class 2606 OID 1092340)
-- Name: message_attachment_attachment_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY message_attachment
    ADD CONSTRAINT message_attachment_attachment_id_fk FOREIGN KEY (attachment_id) REFERENCES attachment(id) ON DELETE CASCADE;


--
-- TOC entry 2018 (class 2606 OID 1092345)
-- Name: message_attachment_message_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY message_attachment
    ADD CONSTRAINT message_attachment_message_id_fk FOREIGN KEY (message_id) REFERENCES message(id) ON DELETE CASCADE;


--
-- TOC entry 2014 (class 2606 OID 1100473)
-- Name: message_conversation_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY message
    ADD CONSTRAINT message_conversation_id_fk FOREIGN KEY (conversation_id) REFERENCES conversation(id);


--
-- TOC entry 2015 (class 2606 OID 1092376)
-- Name: message_group_member_member_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY message_group_member
    ADD CONSTRAINT message_group_member_member_id_fk FOREIGN KEY (member_id) REFERENCES member(id);


--
-- TOC entry 2016 (class 2606 OID 1100478)
-- Name: message_group_member_message_group_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY message_group_member
    ADD CONSTRAINT message_group_member_message_group_id_fk FOREIGN KEY (group_id) REFERENCES message_group(id);


--
-- TOC entry 2013 (class 2606 OID 1092362)
-- Name: message_message_service_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY message
    ADD CONSTRAINT message_message_service_id_fk FOREIGN KEY (service_id) REFERENCES message_service(id);


-- Completed on 2017-01-17 18:17:46

--
-- PostgreSQL database dump complete
--


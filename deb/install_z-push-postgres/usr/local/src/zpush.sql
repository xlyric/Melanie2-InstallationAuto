--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'SQL_ASCII';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner:
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner:
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: settings; Type: TABLE; Schema: public; Owner: zpush; Tablespace:
--

CREATE TABLE public.settings (
    key_name character varying(50) NOT NULL,
    key_value character varying(50) NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


ALTER TABLE public.settings OWNER TO zpush;

--
-- Name: states; Type: TABLE; Schema: public; Owner: zpush; Tablespace:
--

CREATE TABLE public.states (
    id_state_seq integer NOT NULL,
    device_id character varying(50) NOT NULL,
    uuid character varying(50),
    state_type character varying(50),
    counter integer,
    state_data bytea,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


ALTER TABLE public.states OWNER TO zpush;

--
-- Name: states_id_state_seq_seq; Type: SEQUENCE; Schema: public; Owner: zpush
--

CREATE SEQUENCE public.states_id_state_seq_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.states_id_state_seq_seq OWNER TO zpush;

--
-- Name: states_id_state_seq_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zpush
--

ALTER SEQUENCE public.states_id_state_seq_seq OWNED BY public.states.id_state_seq;


--
-- Name: users; Type: TABLE; Schema: public; Owner: zpush; Tablespace:
--

CREATE TABLE public.users (
    username character varying(50) NOT NULL,
    device_id character varying(50) NOT NULL
);


ALTER TABLE public.users OWNER TO zpush;

--
-- Name: zpdevices; Type: TABLE; Schema: public; Owner: zpush; Tablespace:
--

CREATE TABLE public.zpdevices (
    deviceid character varying(255) NOT NULL,
    username character varying(255) NOT NULL
);


ALTER TABLE public.zpdevices OWNER TO zpush;

--
-- Name: zpimap; Type: TABLE; Schema: public; Owner: zpush; Tablespace:
--

CREATE TABLE public.zpimap (
    deviceid character varying(255) NOT NULL,
    username character varying(255) NOT NULL,
    imaplastcheck integer,
    imapstatus integer
);


ALTER TABLE public.zpimap OWNER TO zpush;

--
-- Name: zpsearch; Type: TABLE; Schema: public; Owner: zpush; Tablespace:
--

CREATE TABLE public.zpsearch (
    deviceid character varying(255) NOT NULL,
    username character varying(255) NOT NULL,
    searchclass character varying(255) NOT NULL,
    searchfolder character varying(255),
    searchfreetext character varying(255),
    searchresult text,
    searchdeeptraversal boolean
);


ALTER TABLE public.zpsearch OWNER TO zpush;

--
-- Name: zpstate; Type: TABLE; Schema: public; Owner: zpush; Tablespace:
--

CREATE TABLE public.zpstate (
    stateid integer NOT NULL,
    deviceid character varying(255) NOT NULL,
    statetype character varying(255) NOT NULL,
    statekey character varying(255),
    statecounter integer,
    hash integer NOT NULL,
    state text NOT NULL
);


ALTER TABLE public.zpstate OWNER TO zpush;

--
-- Name: zpstate_seq; Type: SEQUENCE; Schema: public; Owner: zpush
--

CREATE SEQUENCE public.zpstate_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.zpstate_seq OWNER TO zpush;

--
-- Name: id_state_seq; Type: DEFAULT; Schema: public; Owner: zpush
--

ALTER TABLE ONLY public.states ALTER COLUMN id_state_seq SET DEFAULT nextval('public.states_id_state_seq_seq'::regclass);


--
-- Data for Name: settings; Type: TABLE DATA; Schema: public; Owner: zpush
--

COPY public.settings (key_name, key_value, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: states; Type: TABLE DATA; Schema: public; Owner: zpush
--

COPY public.states (id_state_seq, device_id, uuid, state_type, counter, state_data, created_at, updated_at) FROM stdin;
\.


--
-- Name: states_id_state_seq_seq; Type: SEQUENCE SET; Schema: public; Owner: zpush
--

SELECT pg_catalog.setval('public.states_id_state_seq_seq', 1, false);


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: zpush
--

COPY public.users (username, device_id) FROM stdin;
\.


--
-- Data for Name: zpdevices; Type: TABLE DATA; Schema: public; Owner: zpush
--

COPY public.zpdevices (deviceid, username) FROM stdin;
\.


--
-- Data for Name: zpimap; Type: TABLE DATA; Schema: public; Owner: zpush
--

COPY public.zpimap (deviceid, username, imaplastcheck, imapstatus) FROM stdin;
\.


--
-- Data for Name: zpsearch; Type: TABLE DATA; Schema: public; Owner: zpush
--

COPY public.zpsearch (deviceid, username, searchclass, searchfolder, searchfreetext, searchresult, searchdeeptraversal) FROM stdin;
\.


--
-- Data for Name: zpstate; Type: TABLE DATA; Schema: public; Owner: zpush
--

COPY public.zpstate (stateid, deviceid, statetype, statekey, statecounter, hash, state) FROM stdin;
\.


--
-- Name: zpstate_seq; Type: SEQUENCE SET; Schema: public; Owner: zpush
--

SELECT pg_catalog.setval('public.zpstate_seq', 1, false);


--
-- Name: settings_pkey; Type: CONSTRAINT; Schema: public; Owner: zpush; Tablespace:
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (key_name);


--
-- Name: states_pkey; Type: CONSTRAINT; Schema: public; Owner: zpush; Tablespace:
--

ALTER TABLE ONLY public.states
    ADD CONSTRAINT states_pkey PRIMARY KEY (id_state_seq);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: zpush; Tablespace:
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (username, device_id);


--
-- Name: zpdevices_pkey; Type: CONSTRAINT; Schema: public; Owner: zpush; Tablespace:
--

ALTER TABLE ONLY public.zpdevices
    ADD CONSTRAINT zpdevices_pkey PRIMARY KEY (deviceid, username);


--
-- Name: zpimap_pkey; Type: CONSTRAINT; Schema: public; Owner: zpush; Tablespace:
--

ALTER TABLE ONLY public.zpimap
    ADD CONSTRAINT zpimap_pkey PRIMARY KEY (deviceid, username);


--
-- Name: zpsearch_pkey; Type: CONSTRAINT; Schema: public; Owner: zpush; Tablespace:
--

ALTER TABLE ONLY public.zpsearch
    ADD CONSTRAINT zpsearch_pkey PRIMARY KEY (deviceid, username, searchclass);


--
-- Name: zpstate_pkey; Type: CONSTRAINT; Schema: public; Owner: zpush; Tablespace:
--

ALTER TABLE ONLY public.zpstate
    ADD CONSTRAINT zpstate_pkey PRIMARY KEY (stateid);


--
-- Name: idx_states_unique; Type: INDEX; Schema: public; Owner: zpush; Tablespace:
--

CREATE UNIQUE INDEX idx_states_unique ON public.states USING btree (device_id, uuid, state_type, counter);


--
-- Name: zpstate_deviceid_idx; Type: INDEX; Schema: public; Owner: zpush; Tablespace:
--

CREATE INDEX zpstate_deviceid_idx ON public.zpstate USING btree (deviceid);


--
-- Name: zpstate_statecounter_idx; Type: INDEX; Schema: public; Owner: zpush; Tablespace:
--

CREATE INDEX zpstate_statecounter_idx ON public.zpstate USING btree (statecounter);


--
-- Name: zpstate_statekey_idx; Type: INDEX; Schema: public; Owner: zpush; Tablespace:
--

CREATE INDEX zpstate_statekey_idx ON public.zpstate USING btree (statekey);


--
-- Name: zpstate_statetype_idx; Type: INDEX; Schema: public; Owner: zpush; Tablespace:
--

CREATE INDEX zpstate_statetype_idx ON public.zpstate USING btree (statetype);


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--


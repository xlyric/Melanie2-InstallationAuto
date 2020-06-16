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


--
-- Name: update_calendar_ctag(); Type: FUNCTION; Schema: public; Owner: roundcube
--

CREATE FUNCTION public.update_calendar_ctag() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    calendar_ctag varchar;
    p_calendar_id varchar;
    p_event_uid varchar;
    p_action varchar;
    a_datatree_synctoken bigint;
BEGIN
    IF (TG_OP = 'DELETE') THEN
                p_calendar_id := OLD.calendar_id;
                p_event_uid := OLD.event_uid;
                p_action := 'del';
    ELSIF (TG_OP = 'INSERT') THEN
                p_calendar_id := NEW.calendar_id;
                p_event_uid := NEW.event_uid;
                p_action := 'add';
    ELSIF (TG_OP = 'UPDATE') THEN
                p_calendar_id := NEW.calendar_id;
                p_event_uid := NEW.event_uid;
                p_action := 'mod';
    END IF;

    SELECT md5(CAST(sum(k.event_modified) AS varchar)) INTO calendar_ctag FROM kronolith_events k WHERE k.calendar_id = p_calendar_id;
    IF NOT FOUND THEN
        calendar_ctag := md5(p_calendar_id);
    END IF;
    UPDATE horde_datatree SET datatree_ctag = calendar_ctag, datatree_synctoken = datatree_synctoken + 1 WHERE datatree_name = p_calendar_id AND group_uid = 'horde.shares.kronolith';
    SELECT datatree_synctoken INTO a_datatree_synctoken FROM horde_datatree WHERE datatree_name = p_calendar_id AND group_uid = 'horde.shares.kronolith';
    IF FOUND THEN
        INSERT INTO kronolith_sync VALUES (a_datatree_synctoken, p_calendar_id, p_event_uid, p_action);
    END IF;

    IF (TG_OP = 'DELETE') THEN
        RETURN OLD;
    ELSE
        RETURN NEW;
    END IF;
END;
$$;


ALTER FUNCTION public.update_calendar_ctag() OWNER TO roundcube;

--
-- Name: update_taskslist_ctag(); Type: FUNCTION; Schema: public; Owner: roundcube
--

CREATE FUNCTION public.update_taskslist_ctag() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    taskslist_ctag varchar;
    p_taskslist_id varchar;
    p_task_uid varchar;
    p_action varchar;
    a_datatree_synctoken bigint;
BEGIN
    IF (TG_OP = 'DELETE') THEN
                p_taskslist_id := OLD.task_owner;
                p_task_uid := OLD.task_uid;
                p_action := 'del';
    ELSIF (TG_OP = 'INSERT') THEN
                p_taskslist_id := NEW.task_owner;
                p_task_uid := NEW.task_uid;
                p_action := 'add';
    ELSIF (TG_OP = 'UPDATE') THEN
                p_taskslist_id := NEW.task_owner;
                p_task_uid := NEW.task_uid;
                p_action := 'mod';
    END IF;

    SELECT md5(CAST(sum(n.task_ts) AS varchar)) INTO taskslist_ctag FROM nag_tasks n WHERE n.task_owner = p_taskslist_id;
    IF NOT FOUND THEN
        taskslist_ctag := md5(p_taskslist_id);
    END IF;
    UPDATE horde_datatree SET datatree_ctag = taskslist_ctag, datatree_synctoken = datatree_synctoken + 1 WHERE datatree_name = p_taskslist_id AND group_uid = 'horde.shares.nag';
    SELECT datatree_synctoken INTO a_datatree_synctoken FROM horde_datatree WHERE datatree_name = p_taskslist_id AND group_uid = 'horde.shares.nag';
    IF FOUND THEN
        INSERT INTO nag_sync VALUES (a_datatree_synctoken, p_taskslist_id, p_task_uid, p_action);
    END IF;

    IF (TG_OP = 'DELETE') THEN
        RETURN OLD;
    ELSE
        RETURN NEW;
    END IF;
END;
$$;


ALTER FUNCTION public.update_taskslist_ctag() OWNER TO roundcube;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.cache (
    user_id integer NOT NULL,
    cache_key character varying(128) DEFAULT ''::character varying NOT NULL,
    expires timestamp with time zone,
    data text NOT NULL
);


ALTER TABLE public.cache OWNER TO roundcube;

--
-- Name: cache_index; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.cache_index (
    user_id integer NOT NULL,
    mailbox character varying(255) NOT NULL,
    expires timestamp with time zone,
    valid smallint DEFAULT 0 NOT NULL,
    data text NOT NULL
);


ALTER TABLE public.cache_index OWNER TO roundcube;

--
-- Name: cache_messages; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.cache_messages (
    user_id integer NOT NULL,
    mailbox character varying(255) NOT NULL,
    uid integer NOT NULL,
    expires timestamp with time zone,
    data text NOT NULL,
    flags integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.cache_messages OWNER TO roundcube;

--
-- Name: cache_shared; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.cache_shared (
    cache_key character varying(255) NOT NULL,
    expires timestamp with time zone,
    data text NOT NULL
);


ALTER TABLE public.cache_shared OWNER TO roundcube;

--
-- Name: cache_thread; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.cache_thread (
    user_id integer NOT NULL,
    mailbox character varying(255) NOT NULL,
    expires timestamp with time zone,
    data text NOT NULL
);


ALTER TABLE public.cache_thread OWNER TO roundcube;

--
-- Name: contactgroupmembers; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.contactgroupmembers (
    contactgroup_id integer NOT NULL,
    contact_id integer NOT NULL,
    created timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.contactgroupmembers OWNER TO roundcube;

--
-- Name: contactgroups; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.contactgroups (
    contactgroup_id integer DEFAULT nextval(('contactgroups_seq'::text)::regclass) NOT NULL,
    user_id integer NOT NULL,
    changed timestamp with time zone DEFAULT now() NOT NULL,
    del smallint DEFAULT 0 NOT NULL,
    name character varying(128) DEFAULT ''::character varying NOT NULL
);


ALTER TABLE public.contactgroups OWNER TO roundcube;

--
-- Name: contactgroups_seq; Type: SEQUENCE; Schema: public; Owner: roundcube
--

CREATE SEQUENCE public.contactgroups_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.contactgroups_seq OWNER TO roundcube;

--
-- Name: contacts; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.contacts (
    contact_id integer DEFAULT nextval(('contacts_seq'::text)::regclass) NOT NULL,
    user_id integer NOT NULL,
    changed timestamp with time zone DEFAULT now() NOT NULL,
    del smallint DEFAULT 0 NOT NULL,
    name character varying(128) DEFAULT ''::character varying NOT NULL,
    email text DEFAULT ''::text NOT NULL,
    firstname character varying(128) DEFAULT ''::character varying NOT NULL,
    surname character varying(128) DEFAULT ''::character varying NOT NULL,
    vcard text,
    words text
);


ALTER TABLE public.contacts OWNER TO roundcube;

--
-- Name: contacts_seq; Type: SEQUENCE; Schema: public; Owner: roundcube
--

CREATE SEQUENCE public.contacts_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.contacts_seq OWNER TO roundcube;

--
-- Name: dictionary; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.dictionary (
    user_id integer,
    language character varying(5) NOT NULL,
    data text NOT NULL
);


ALTER TABLE public.dictionary OWNER TO roundcube;

--
-- Name: identities; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.identities (
    identity_id integer DEFAULT nextval(('identities_seq'::text)::regclass) NOT NULL,
    user_id integer NOT NULL,
    changed timestamp with time zone DEFAULT now() NOT NULL,
    del smallint DEFAULT 0 NOT NULL,
    standard smallint DEFAULT 0 NOT NULL,
    name character varying(128) NOT NULL,
    organization character varying(128),
    email character varying(128) NOT NULL,
    "reply-to" character varying(128),
    bcc character varying(128),
    signature text,
    html_signature integer DEFAULT 0 NOT NULL,
    realname text DEFAULT ''::text,
    uid text DEFAULT ''::text
);


ALTER TABLE public.identities OWNER TO roundcube;

--
-- Name: identities_seq; Type: SEQUENCE; Schema: public; Owner: roundcube
--

CREATE SEQUENCE public.identities_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.identities_seq OWNER TO roundcube;

--
-- Name: kronolith_sync; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.kronolith_sync (
    token bigint NOT NULL,
    calendar_id character varying(255) NOT NULL,
    event_uid character varying(255) NOT NULL,
    action character varying(3) NOT NULL
);


ALTER TABLE public.kronolith_sync OWNER TO roundcube;

--
-- Name: nag_sync; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.nag_sync (
    token bigint NOT NULL,
    taskslist_id character varying(255) NOT NULL,
    task_uid character varying(255) NOT NULL,
    action character varying(3) NOT NULL
);


ALTER TABLE public.nag_sync OWNER TO roundcube;

--
-- Name: pamela_mailcount; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.pamela_mailcount (
    uid character varying(255) NOT NULL,
    send_time timestamp without time zone NOT NULL,
    nb_dest integer DEFAULT 0 NOT NULL,
    address_ip character varying(16) DEFAULT '0.0.0.0'::character varying NOT NULL
);


ALTER TABLE public.pamela_mailcount OWNER TO roundcube;

--
-- Name: pamela_tentativescnx; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.pamela_tentativescnx (
    uid character varying(128) NOT NULL,
    lastcnx integer NOT NULL,
    nbtentatives integer NOT NULL
);


ALTER TABLE public.pamela_tentativescnx OWNER TO roundcube;

--
-- Name: searches; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.searches (
    search_id integer DEFAULT nextval(('searches_seq'::text)::regclass) NOT NULL,
    user_id integer NOT NULL,
    type smallint DEFAULT 0 NOT NULL,
    name character varying(128) NOT NULL,
    data text NOT NULL
);


ALTER TABLE public.searches OWNER TO roundcube;

--
-- Name: searches_seq; Type: SEQUENCE; Schema: public; Owner: roundcube
--

CREATE SEQUENCE public.searches_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.searches_seq OWNER TO roundcube;

--
-- Name: session; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.session (
    sess_id character varying(128) DEFAULT ''::character varying NOT NULL,
    changed timestamp with time zone DEFAULT now() NOT NULL,
    ip character varying(41) NOT NULL,
    vars text NOT NULL
);


ALTER TABLE public.session OWNER TO roundcube;

--
-- Name: system; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.system (
    name character varying(64) NOT NULL,
    value text
);


ALTER TABLE public.system OWNER TO roundcube;

--
-- Name: users; Type: TABLE; Schema: public; Owner: roundcube; Tablespace:
--

CREATE TABLE public.users (
    user_id integer DEFAULT nextval(('users_seq'::text)::regclass) NOT NULL,
    username character varying(128) DEFAULT ''::character varying NOT NULL,
    mail_host character varying(128) DEFAULT ''::character varying NOT NULL,
    created timestamp with time zone DEFAULT now() NOT NULL,
    last_login timestamp with time zone,
    language character varying(5),
    preferences text DEFAULT ''::text NOT NULL,
    failed_login timestamp with time zone,
    failed_login_counter integer
);


ALTER TABLE public.users OWNER TO roundcube;

--
-- Name: users_seq; Type: SEQUENCE; Schema: public; Owner: roundcube
--

CREATE SEQUENCE public.users_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_seq OWNER TO roundcube;

--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.cache (user_id, cache_key, expires, data) FROM stdin;
\.


--
-- Data for Name: cache_index; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.cache_index (user_id, mailbox, expires, valid, data) FROM stdin;
\.


--
-- Data for Name: cache_messages; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.cache_messages (user_id, mailbox, uid, expires, data, flags) FROM stdin;
\.


--
-- Data for Name: cache_shared; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.cache_shared (cache_key, expires, data) FROM stdin;
\.


--
-- Data for Name: cache_thread; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.cache_thread (user_id, mailbox, expires, data) FROM stdin;
\.


--
-- Data for Name: contactgroupmembers; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.contactgroupmembers (contactgroup_id, contact_id, created) FROM stdin;
\.


--
-- Data for Name: contactgroups; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.contactgroups (contactgroup_id, user_id, changed, del, name) FROM stdin;
\.


--
-- Name: contactgroups_seq; Type: SEQUENCE SET; Schema: public; Owner: roundcube
--

SELECT pg_catalog.setval('public.contactgroups_seq', 1, false);


--
-- Data for Name: contacts; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.contacts (contact_id, user_id, changed, del, name, email, firstname, surname, vcard, words) FROM stdin;
\.


--
-- Name: contacts_seq; Type: SEQUENCE SET; Schema: public; Owner: roundcube
--

SELECT pg_catalog.setval('public.contacts_seq', 1, false);


--
-- Data for Name: dictionary; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.dictionary (user_id, language, data) FROM stdin;
\.


--
-- Data for Name: identities; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.identities (identity_id, user_id, changed, del, standard, name, organization, email, "reply-to", bcc, signature, html_signature, realname, uid) FROM stdin;
1       1       2020-05-28 23:20:18.877401+00   0       1       test1   \N      test1@mce.com   \N      \N      \N      0       test1   test1
2       2       2020-05-28 23:25:59.792789+00   0       1       test3   \N      test3@mce.com   \N      \N      \N      0       test3   test3
3       3       2020-06-03 07:56:59.775619+00   0       1       test4   \N      test4@mce.com   \N      \N      \N      0       test4   test4
4       4       2020-06-03 13:16:00.504029+00   0       1       test2   \N      test2@mce.com   \N      \N      \N      0       test2   test2
\.


--
-- Name: identities_seq; Type: SEQUENCE SET; Schema: public; Owner: roundcube
--

SELECT pg_catalog.setval('public.identities_seq', 4, true);


--
-- Data for Name: kronolith_sync; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.kronolith_sync (token, calendar_id, event_uid, action) FROM stdin;
\.


--
-- Data for Name: nag_sync; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.nag_sync (token, taskslist_id, task_uid, action) FROM stdin;
\.


--
-- Data for Name: pamela_mailcount; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.pamela_mailcount (uid, send_time, nb_dest, address_ip) FROM stdin;
\.


--
-- Data for Name: pamela_tentativescnx; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.pamela_tentativescnx (uid, lastcnx, nbtentatives) FROM stdin;
\.


--
-- Data for Name: searches; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.searches (search_id, user_id, type, name, data) FROM stdin;
\.


--
-- Name: searches_seq; Type: SEQUENCE SET; Schema: public; Owner: roundcube
--

SELECT pg_catalog.setval('public.searches_seq', 1, false);


--
-- Data for Name: session; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.session (sess_id, changed, ip, vars) FROM stdin;
\.


--
-- Data for Name: system; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.system (name, value) FROM stdin;
roundcube-version       2015030800
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: roundcube
--

COPY public.users (user_id, username, mail_host, created, last_login, language, preferences, failed_login, failed_login_counter) FROM stdin;
1       test1   web-srv.mce.com 2020-05-28 23:20:18.82549+00    2020-06-16 08:04:07.657442+00   fr_FR   a:13:{s:15:"color_calendars";a:2:{s:5:"test1";s:6:"3C8A9A";s:5:"test3";s:6:"A21A16";}s:16:"active_calendars";a:2:{s:5:"test3";i:1;s:5:"test1";i:1;}s:15:"alarm_calendars";a:0:{}s:14:"compose_extwin";i:0;s:10:"htmleditor";i:3;s:11:"mdn_default";b:0;s:11:"dsn_default";b:0;s:25:"compose_save_localstorage";i:1;s:15:"color_tasklists";a:1:{s:5:"test1";s:6:"8C4FEF";}s:16:"active_tasklists";a:1:{s:5:"test1";i:1;}s:15:"alarm_tasklists";a:1:{s:5:"test1";i:1;}s:19:"default_addressbook";s:5:"test1";s:11:"client_hash";s:32:"2c9f7d7860a80903f34fb946a7e9afed";}     \N      \N
3       test4   web-srv.mce.com 2020-06-03 07:56:59.747693+00   2020-06-03 13:17:13.127196+00   fr_FR   a:1:{s:11:"client_hash";s:32:"0169a195c70a46465f02ce0d71d0b8e7";}    \N      \N
2       test3   web-srv.mce.com 2020-05-28 23:25:59.771315+00   2020-06-03 13:17:31.116311+00   fr_FR   a:9:{s:15:"color_calendars";a:1:{s:5:"test3";s:6:"B43765";}s:16:"active_calendars";a:0:{}s:15:"alarm_calendars";a:0:{}s:15:"color_tasklists";a:1:{s:5:"test3";s:6:"975EDA";}s:16:"active_tasklists";a:1:{s:5:"test3";i:1;}s:15:"alarm_tasklists";a:1:{s:5:"test3";i:1;}s:19:"default_addressbook";s:5:"test3";s:16:"hidden_mailboxes";a:0:{}s:11:"client_hash";s:32:"3300bf522bc5b55f171c1b67bdbac268";}       \N      \N
4       test2   web-srv.mce.com 2020-06-03 13:16:00.481746+00   2020-06-04 09:28:10.046816+00   fr_FR   a:9:{s:14:"compose_extwin";i:0;s:10:"htmleditor";i:3;s:11:"mdn_default";b:0;s:11:"dsn_default";b:0;s:25:"compose_save_localstorage";i:1;s:15:"color_calendars";a:1:{s:5:"test2";s:6:"7A6808";}s:16:"active_calendars";a:0:{}s:15:"alarm_calendars";a:0:{}s:11:"client_hash";s:32:"5b14978313a7bb44cf241763857e5e88";}     \N  \N
\.


--
-- Name: users_seq; Type: SEQUENCE SET; Schema: public; Owner: roundcube
--

SELECT pg_catalog.setval('public.users_seq', 4, true);


--
-- Name: cache_index_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.cache_index
    ADD CONSTRAINT cache_index_pkey PRIMARY KEY (user_id, mailbox);


--
-- Name: cache_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.cache_messages
    ADD CONSTRAINT cache_messages_pkey PRIMARY KEY (user_id, mailbox, uid);


--
-- Name: cache_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (user_id, cache_key);


--
-- Name: cache_shared_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.cache_shared
    ADD CONSTRAINT cache_shared_pkey PRIMARY KEY (cache_key);


--
-- Name: cache_thread_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.cache_thread
    ADD CONSTRAINT cache_thread_pkey PRIMARY KEY (user_id, mailbox);


--
-- Name: contactgroupmembers_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.contactgroupmembers
    ADD CONSTRAINT contactgroupmembers_pkey PRIMARY KEY (contactgroup_id, contact_id);


--
-- Name: contactgroups_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.contactgroups
    ADD CONSTRAINT contactgroups_pkey PRIMARY KEY (contactgroup_id);


--
-- Name: contacts_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.contacts
    ADD CONSTRAINT contacts_pkey PRIMARY KEY (contact_id);


--
-- Name: dictionary_user_id_language_key; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.dictionary
    ADD CONSTRAINT dictionary_user_id_language_key UNIQUE (user_id, language);


--
-- Name: identities_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.identities
    ADD CONSTRAINT identities_pkey PRIMARY KEY (identity_id);


--
-- Name: kronolith_sync_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.kronolith_sync
    ADD CONSTRAINT kronolith_sync_pkey PRIMARY KEY (token, calendar_id);


--
-- Name: nag_sync_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.nag_sync
    ADD CONSTRAINT nag_sync_pkey PRIMARY KEY (token, taskslist_id);


--
-- Name: pamela_tentativescnx_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.pamela_tentativescnx
    ADD CONSTRAINT pamela_tentativescnx_pkey PRIMARY KEY (uid);


--
-- Name: searches_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.searches
    ADD CONSTRAINT searches_pkey PRIMARY KEY (search_id);


--
-- Name: searches_user_id_key; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.searches
    ADD CONSTRAINT searches_user_id_key UNIQUE (user_id, type, name);


--
-- Name: session_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.session
    ADD CONSTRAINT session_pkey PRIMARY KEY (sess_id);


--
-- Name: system_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.system
    ADD CONSTRAINT system_pkey PRIMARY KEY (name);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (user_id);


--
-- Name: users_username_key; Type: CONSTRAINT; Schema: public; Owner: roundcube; Tablespace:
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username, mail_host);


--
-- Name: cache_expires_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX cache_expires_idx ON public.cache USING btree (expires);


--
-- Name: cache_index_expires_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX cache_index_expires_idx ON public.cache_index USING btree (expires);


--
-- Name: cache_messages_expires_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX cache_messages_expires_idx ON public.cache_messages USING btree (expires);


--
-- Name: cache_shared_expires_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX cache_shared_expires_idx ON public.cache_shared USING btree (expires);


--
-- Name: cache_thread_expires_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX cache_thread_expires_idx ON public.cache_thread USING btree (expires);


--
-- Name: contactgroupmembers_contact_id_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX contactgroupmembers_contact_id_idx ON public.contactgroupmembers USING btree (contact_id);


--
-- Name: contactgroups_user_id_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX contactgroups_user_id_idx ON public.contactgroups USING btree (user_id, del);


--
-- Name: contacts_user_id_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX contacts_user_id_idx ON public.contacts USING btree (user_id, del);


--
-- Name: identities_email_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX identities_email_idx ON public.identities USING btree (email, del);


--
-- Name: identities_user_id_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX identities_user_id_idx ON public.identities USING btree (user_id, del);


--
-- Name: pamela_mailcount_nb_dest_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX pamela_mailcount_nb_dest_idx ON public.pamela_mailcount USING btree (nb_dest);


--
-- Name: pamela_mailcount_send_time_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX pamela_mailcount_send_time_idx ON public.pamela_mailcount USING btree (send_time);


--
-- Name: pamela_mailcount_uid_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX pamela_mailcount_uid_idx ON public.pamela_mailcount USING btree (uid);


--
-- Name: session_changed_idx; Type: INDEX; Schema: public; Owner: roundcube; Tablespace:
--

CREATE INDEX session_changed_idx ON public.session USING btree (changed);


--
-- Name: cache_index_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: roundcube
--

ALTER TABLE ONLY public.cache_index
    ADD CONSTRAINT cache_index_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: cache_messages_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: roundcube
--

ALTER TABLE ONLY public.cache_messages
    ADD CONSTRAINT cache_messages_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: cache_thread_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: roundcube
--

ALTER TABLE ONLY public.cache_thread
    ADD CONSTRAINT cache_thread_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: cache_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: roundcube
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: contactgroupmembers_contact_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: roundcube
--

ALTER TABLE ONLY public.contactgroupmembers
    ADD CONSTRAINT contactgroupmembers_contact_id_fkey FOREIGN KEY (contact_id) REFERENCES public.contacts(contact_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: contactgroupmembers_contactgroup_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: roundcube
--

ALTER TABLE ONLY public.contactgroupmembers
    ADD CONSTRAINT contactgroupmembers_contactgroup_id_fkey FOREIGN KEY (contactgroup_id) REFERENCES public.contactgroups(contactgroup_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: contactgroups_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: roundcube
--

ALTER TABLE ONLY public.contactgroups
    ADD CONSTRAINT contactgroups_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: contacts_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: roundcube
--

ALTER TABLE ONLY public.contacts
    ADD CONSTRAINT contacts_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: dictionary_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: roundcube
--

ALTER TABLE ONLY public.dictionary
    ADD CONSTRAINT dictionary_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: identities_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: roundcube
--

ALTER TABLE ONLY public.identities
    ADD CONSTRAINT identities_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: searches_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: roundcube
--

ALTER TABLE ONLY public.searches
    ADD CONSTRAINT searches_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


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

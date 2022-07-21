-- Sequence and defined type
CREATE SEQUENCE IF NOT EXISTS all_examples_id_seq;

-- Table Definition
CREATE TABLE "public"."all_examples" (
    "id" int4 NOT NULL DEFAULT nextval('all_examples_id_seq'::regclass),
    "aggregate_id" uuid NOT NULL,
    "name" varchar NOT NULL,
    "region" varchar NOT NULL,
    "created_at" timestamptz,
    "updated_at" timestamptz,
    PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "aggregate_all_examples_id_index" ON "public"."all_examples" USING BTREE ("aggregate_id");

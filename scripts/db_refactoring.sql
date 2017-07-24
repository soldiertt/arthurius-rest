alter table product change promo youtube_ref varchar(30);
alter table product add column promo boolean NOT NULL default false;
alter table product add column old_price float;
alter table product add column instock boolean NOT NULL default true;
alter table product change piece comment varchar(100);
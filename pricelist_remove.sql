DELETE FROM configuration WHERE configuration_key LIKE 'PL_%';
DELETE FROM configuration_group WHERE configuration_group_title='Printable Price-list';
DELETE FROM configuration_group WHERE configuration_group_title LIKE 'Price-list Profile-%';
DELETE FROM admin_pages WHERE page_key LIKE 'config%Pricelist%';
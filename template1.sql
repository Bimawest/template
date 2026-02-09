ALTER TABLE templates 
ADD COLUMN nama_template VARCHAR(100) AFTER id,
ADD COLUMN isi_html LONGTEXT AFTER nama_template,
ADD COLUMN thumbnail VARCHAR(255) AFTER isi_html;

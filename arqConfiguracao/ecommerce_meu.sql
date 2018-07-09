use cursolaravel2;
select * from users;
select * from posts;

-- DROP DATABASE db_ecommerce;
use db_ecommerce;

select * from tb_users;
select * from tb_persons;
select * from tb_products;
select * from tb_categories;

SELECT * FROM tb_userspasswordsrecoveries;

SELECT *
FROM tb_userspasswordsrecoveries a
INNER JOIN tb_users b USING(iduser)
INNER JOIN tb_persons c USING(idperson)
WHERE
	a.idrecovery = 2
    AND
    a.dtrecovery IS NOT NULL
    AND
    DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();

SELECT * FROM tb_products WHERE idproduct IN(
	SELECT a.idproduct
    FROM tb_products a
	INNER JOIN tb_productscategories b on a.idproduct = b.idproduct
	WHERE b.idcategory = 3
);

--

SELECT sql_calc_found_rows * FROM tb_products a
INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
INNER JOIN tb_categories c ON c.idcategory = b.idcategory
WHERE c.idcategory = 1
LIMIT 3;

SELECT found_rows() AS nrtotal;

--

SELECT * from tb_products WHERE desurl = 'ipad-32gb';
--update answersnumbers set userresult = SUBSTRING_INDEX(operationlog,'=',-1) where points > 0


-- tam sonuca ulasma yuzdesine gore kullanicilar
select u.username, 100*count(a.id) / (select count(*) from answersnumbers where userid=a.userid) as basariYuzde, count(*) as oyunSayisi from answersnumbers a, users u where u.id = a.userid and a.targetnumber=a.userresult group by u.username having count(*) > 1 order by basariYuzde desc, oyunSayisi asc limit 100;

-- puan alma yuzdesine gore kullanicilar
select u.username, 100*count(a.id) / (select count(*) from answersnumbers where userid=a.userid) as basariYuzde from answersnumbers a, users u where u.id = a.userid and a.points>0 group by u.username order by basariYuzde desc limit 100;

-- tam sonuca ortalama olarak en kısa sürede ulaşan kullanıcılar
select u.username, avg(75 - remainingtime) as ortalamaHiz, count(*) as yarismaSayisi from answersnumbers a, users u where u.id = a.userid and points > 0 and targetnumber=userresult group by u.username having yarismaSayisi > 5 order by ortalamaHiz limit 100;

-- puan aldığı bir soruyu ortalama alarak en kisa sürede çözen kullanıcılar, en az 5 oyun oynayan kullanicilar arasinda
select u.username, avg(75 - remainingtime) as ortalamaHiz, count(*) as yarismaSayisi from answersnumbers a, users u where u.id = a.userid and points > 0 group by u.username having yarismaSayisi > 5 order by ortalamaHiz limit 100;

-- ortalama puana gore kullanicilar en az 5 defa oyun oynayan kullanicilar arasinda
select u.username, avg(points) as ortalamaPuan, count(*) as yarismaSayisi from answersnumbers a, users u where u.id = a.userid group by u.username having yarismaSayisi > 5  order by ortalamaPuan desc limit 100;

-- toplam puana gore kullanicilar
select u.username, sum(points) as toplampuan, count(*) as yarismaSayisi from answersnumbers a, users u where u.id = a.userid group by u.username having yarismaSayisi > 5 order by toplamPuan desc limit 100;

-- en fazla oyun oynayan kullanicilar
select u.username, count(*) as oyunSayisi, count(*) as yarismaSayisi from answersnumbers a, users u where u.id = a.userid group by u.username having yarismaSayisi > 5 order by oyunSayisi desc limit 100;

-- tam sonuca en fazla ulaşan kullanıcılar
select u.username, count(*) as oyunSayisi from answersnumbers a, users u where u.id = a.userid and a.targetnumber=a.userresult group by u.username order by oyunSayisi  desc limit 100;

select * from answersnumbers;



-- genel ortalama islem sonucu puani
select * from
        (select count(*) as toplamOyunSayisi from answersnumbers) as toplam,
                (select group_concat(if(yaklasik=0,sayi, NULL)) AS 'tamSonuc',
                        group_concat(if(yaklasik=1,sayi, NULL)) AS 'yaklasik1',
                        group_concat(if(yaklasik=2,sayi, NULL)) AS 'yaklasik2',
                        group_concat(if(yaklasik=3,sayi, NULL)) AS 'yaklasik3',
                        group_concat(if(yaklasik=4,sayi, NULL)) AS 'yaklasik4',
                        group_concat(if(yaklasik=5,sayi, NULL)) AS 'yaklasik5'
                  from (select abs(t.targetnumber-t.userresult) as yaklasik, count(*) sayi from answersnumbers t where points > 0 and userresult > 0 group by yaklasik) as yaklasiklar) AS DIGER,
                (select count(*) as sifirPuan from answersnumbers t where points = 0) as sifirPuan;



-- tevfik1 kullanicisinin puani
SET @user_id=(select id from users where username = 'tevfik1');
select * from
        (select count(*) as toplamOyunSayisi from answersnumbers where userid=@user_id) as toplam,
                (select group_concat(if(yaklasik=0,sayi, NULL)) AS 'tamSonuc',
                        group_concat(if(yaklasik=1,sayi, NULL)) AS 'yaklasik1',
                        group_concat(if(yaklasik=2,sayi, NULL)) AS 'yaklasik2',
                        group_concat(if(yaklasik=3,sayi, NULL)) AS 'yaklasik3',
                        group_concat(if(yaklasik=4,sayi, NULL)) AS 'yaklasik4',
                        group_concat(if(yaklasik=5,sayi, NULL)) AS 'yaklasik5'
                  from (select abs(t.targetnumber-t.userresult) as yaklasik, count(*) sayi from answersnumbers t where points > 0 and userresult > 0 and userid=@user_id group by yaklasik) as yaklasiklar) AS DIGER,
                (select count(*) as sifirPuan from answersnumbers t where points = 0 and userid=@user_id) as sifirPuan;


-- kullanıcı istatistikleri islem
select (tamSonuc+yaklasik1+yaklasik2+yaklasik3+yaklasik4+yaklasik5+sifirPuan) toplamIslemOyunSayisi, islemOzet.* from
        (select ifnull(group_concat(if(yaklasik=0,sayi, NULL)),0) AS  tamSonuc,
                ifnull(group_concat(if(yaklasik=1,sayi, NULL)),0) AS  yaklasik1,
                ifnull(group_concat(if(yaklasik=2,sayi, NULL)),0) AS  yaklasik2,
                ifnull(group_concat(if(yaklasik=3,sayi, NULL)),0) AS  yaklasik3,
                ifnull(group_concat(if(yaklasik=4,sayi, NULL)),0) AS  yaklasik4,
                ifnull(group_concat(if(yaklasik=5,sayi, NULL)),0) AS  yaklasik5,
                sifirPuan
          from (select abs(t.targetnumber-t.userresult) as yaklasik, count(*) sayi from answersnumbers t where points > 0 and userresult > 0 and userid=23 group by yaklasik) yaklasiklar,
                  (select count(*) as sifirPuan from answersnumbers t where points = 0 and userid=23) as sifirPuan) islemOzet;

-- kullanıcı istatistikleri kelime
select (harf3 + harf4 + harf5 + harf6 + harf7 + harf8 + harf9 + harf10 + harf11 + sifirPuan) as toplamKelimeOyunuSayisi, kelimeOzet.* from (select
        ifnull(group_concat(if(kelimeuzunlugu=3,sayi, NULL)),0) AS  harf3,
        ifnull(group_concat(if(kelimeuzunlugu=4,sayi, NULL)),0) AS  harf4,
        ifnull(group_concat(if(kelimeuzunlugu=5,sayi, NULL)),0) AS  harf5,
        ifnull(group_concat(if(kelimeuzunlugu=6,sayi, NULL)),0) AS  harf6,
        ifnull(group_concat(if(kelimeuzunlugu=7,sayi, NULL)),0) AS  harf7,
        ifnull(group_concat(if(kelimeuzunlugu=8,sayi, NULL)),0) AS  harf8,
        ifnull(group_concat(if(kelimeuzunlugu=9,sayi, NULL)),0) AS  harf9,
        ifnull(group_concat(if(kelimeuzunlugu=10,sayi, NULL)),0) AS  harf10,
        ifnull(group_concat(if(kelimeuzunlugu=11,sayi, NULL)),0) AS  harf11,
        sifirPuan
  from (select length(answer) as kelimeuzunlugu, count(*) sayi from answerswords t where points > 0  and userid=23 group by kelimeuzunlugu) kelimeUzunlugu,
          (select count(*) as sifirPuan from answerswords t where points = 0 and userid=23) as sifirPuan) kelimeOzet;

-- kullanıcı istatistikleri ortalama
select  (select date_format(createdate, '%d/%m/%Y - %H:%i:%s') from users where id=23) as kayitTarihi,
        (select (count(*)) from answersnumbers where userid=23) as toplamIslemOyunuSayisi,
        (select (count(*)) from answerswords where userid=23) as toplamKelimeOyunuSayisi,
        (select round(toplamIslemOyunuSayisi/ DATEDIFF(CURDATE(), '2013-01-01'), 2)) as gunlukOrtalamaIslemOyunuSayisi,
        (select round(toplamKelimeOyunuSayisi/ DATEDIFF(CURDATE(), '2013-01-01'),2)) as gunlukOrtalamaKelimeOyunuSayisi,
        (select round((100*count(a.id) /toplamIslemOyunuSayisi), 2) from answersnumbers a, users u where u.id=23 and u.id = a.userid and a.points>0) as islemPuanAlmaYuzdesi,
        (select round((100*count(a.id) /toplamIslemOyunuSayisi) ,2) from answersnumbers a, users u where u.id=23 and u.id = a.userid and a.points>0 and a.userresult = a.targetnumber) as islemTamSonucaUlasmaYuzdesi,
        (select round(avg(remainingtime),2) from answersnumbers a, users u where u.id=23 and  u.id = a.userid and points > 0) as islemOrtalamaKalanSure,
        (select round(avg(remainingtime),2) from answersnumbers a, users u where u.id=23 and  u.id = a.userid and points > 0 and targetnumber=userresult) as islemTamSonucOrtalamaKalanSure,
        (select sum(points) from answersnumbers a, users u where u.id=23 and  u.id = a.userid) as islemToplamPuan,
        (select round(avg(points),2) from answersnumbers a, users u where u.id=23 and  u.id = a.userid) as islemOrtalamaPuan,
        (select sum(points) from answerswords a, users u where u.id=23 and  u.id = a.userid) as kelimeToplamPuan,
        (select round(avg(points),2) from answerswords a, users u where u.id=23 and  u.id = a.userid) as kelimeOrtalamaPuan,
        (select count(a.id) from answerswords a, users u where u.id=23 and a.points > 0 and ((length(a.answer) = 8 and a.answer not like '%?%') or length(a.answer) > 8)) as kelimeTamSonucaUlasmaSayisi,
        (select round(count(a.id)/toplamKelimeOyunuSayisi,2) from answerswords a, users u where u.id=23 and a.points > 0 and ((length(a.answer) = 8 and a.answer not like '%?%') or length(a.answer) > 8)) as kelimeTamSonucaUlasmaYuzdesi,
        (select round((100*count(a.id) /toplamKelimeOyunuSayisi), 2) from answerswords a, users u where u.id=23 and u.id = a.userid and a.points>0) as kelimeSorudanPuanAlmaYuzdesi,
        (select round(avg(remainingtime),2) from answerswords a, users u where u.id=23 and  u.id = a.userid and points > 0) as kelimeOrtalamaKalanSure,
        (select round(avg(length(a.answer)), 2) from answerswords a, users u where u.id=23 and u.id = a.userid and a.points>0) as kelimeOrtalamaHarfSayisi;

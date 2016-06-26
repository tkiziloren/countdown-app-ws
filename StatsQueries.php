<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tkiziloren
 * Date: 09.02.2013
 * Time: 18:39
 * To change this template use File | Settings | File Templates.
 */
require_once("Constants.php");
class StatsQueries
{

    // Ortalama Puana Göre
    public static $QUERY_ISLEM1 = "select u.username as kullaniciAdi, avg(points) as deger, count(*) as sayi from answersnumbers a, users u
                                   where u.id = a.userid group by u.username having sayi > 5  order by deger desc limit 100";

    //Tam Puana Göre
    public static $QUERY_ISLEM2 = "select u.username as kullaniciAdi, sum(points) as deger, count(*) as sayi from answersnumbers a, users u
                                   where u.id = a.userid group by u.username having sayi > 5 order by deger desc limit 100";

    //Tam Sonuca Ulaşma Sayısına Göre
    public static $QUERY_ISLEM3 = "select u.username as kullaniciAdi, count(*) as deger, (select count(*) from answersnumbers where userid=a.userid) as sayi
                                   from answersnumbers a, users u
                                   where u.id = a.userid and a.targetnumber=a.userresult group by u.username order by deger  desc limit 100";

    //Tam Sonuca Ulaşma Yüzdesine Göre
    public static $QUERY_ISLEM4 = "select u.username as kullaniciAdi, (select count(*) from answersnumbers where userid=a.userid) as sayi,
                                   (100*count(a.id)/(select count(*) from answersnumbers where userid=a.userid)) as deger
                                   from answersnumbers a, users u where u.id = a.userid and a.targetnumber=a.userresult group by u.username having sayi > 5
                                   order by deger desc, sayi asc limit 100";

    //Tam Sonuca Ulaşma Hızına Göre
    public static $QUERY_ISLEM5 = "select u.username as kullaniciAdi, avg(75 - remainingtime) as deger,
                                   (select count(*) from answersnumbers where userid=a.userid) as sayi from answersnumbers a, users u
                                   where u.id = a.userid and points > 0 and targetnumber=userresult group by u.username having sayi > 5 order by deger limit 100";

    //Sonuca Ulaşma Hızına Göre
    public static $QUERY_ISLEM6 = "select u.username as kullaniciAdi, avg(75 - remainingtime) as deger,
                                   (select count(*) from answersnumbers where userid=a.userid) as sayi from answersnumbers a, users u
                                   where u.id = a.userid and points > 0 group by u.username having sayi > 5 order by deger limit 100";

    //Sorudan Puan Alma Yüzdesine Göre
    public static $QUERY_ISLEM7 = "select u.username as kullaniciAdi, 100*count(a.id) / (select count(*) from answersnumbers where userid=a.userid) as deger,
                                   (select count(*) from answersnumbers where userid=a.userid) as sayi from answersnumbers a, users u
                                   where u.id = a.userid and a.points>0 group by u.username order by deger desc limit 100";
    //Oyun Sayısına Göre
    public static $QUERY_ISLEM8 = "select u.username as kullaniciAdi, count(*) as deger, 1 as sayi from answersnumbers a, users u
                                   where u.id = a.userid group by u.username order by deger desc";

    public static $QUERY_KELIME1 = "select u.username as kullaniciAdi, avg(points) as deger, count(*) as sayi from answerswords a, users u
                                    where u.id = a.userid group by u.username having sayi > 5  order by deger desc limit 100";

    public static $QUERY_KELIME2 = "select u.username as kullaniciAdi, sum(points) as deger, count(*) as sayi from answerswords a, users u
                                    where u.id = a.userid group by u.username having sayi > 5 order by deger desc limit 100";

    public static $QUERY_KELIME3 = "select u.username as kullaniciAdi, count(*) as deger, (select count(*) from answerswords where userid=a.userid) as sayi
                                    from answerswords a, users u
                                    where u.id = a.userid and a.points > 0 and ((length(a.answer) = 8 and a.answer not like '%?%') or length(a.answer) > 8) group by u.username order by deger  desc limit 100";

    public static $QUERY_KELIME4 = "select u.username as kullaniciAdi, 100*count(a.id)/(select count(*) from answerswords where userid=a.userid) as deger,
                                    (select count(*) from answerswords where userid=a.userid) as sayi
                                    from answerswords a, users u
                                    where u.id = a.userid and a.points > 0 group by u.username order by deger  desc limit 100";

    public static $QUERY_KELIME5 = "select u.username as kullaniciAdi, avg(length(a.answer)) as deger,
                                    (select count(*) from answerswords where userid=a.userid) as sayi
                                    from answerswords a, users u
                                    where u.id = a.userid and a.points > 0 group by u.username order by deger  desc limit 100";

    public static $QUERY_KELIME6 = "select u.username as kullaniciAdi, count(s.id) as deger, (select count(*) from answerswords where userid=u.id)
                                    as sayi from suggestedwords s, users u
                                    where u.username = s.username group by u.username order by deger desc";

    public static $QUERY_KELIME7 = "select u.username as kullaniciAdi, 100*count(s.id)/(select count(*) from suggestedwords where username = u.username) as deger,
                                    (select count(*) from suggestedwords where username = u.username) as sayi from suggestedwords s, users u
                                    where u.username = s.username and s.accepted=1 group by u.username order by deger desc";

    public static $QUERY_KELIME8 = "select u.username as kullaniciAdi, count(*) as deger, 1 as sayi from answersnumbers a, users u
                                    where u.id = a.userid group by u.username order by deger desc";



    public static $QUERY_GENEL = "select    (select (count(*)) from users) as toplamOyuncuSayisi,
                                            (select (count(*)) from answersnumbers) as toplamIslemOyunuSayisi,
                                            (select (count(*)) from answerswords) as toplamKelimeOyunuSayisi,
                                            (select round(toplamIslemOyunuSayisi/ DATEDIFF(CURDATE(), 'GAME_START_DATE'), 2)) as gunlukOrtalamaIslemOyunuSayisi,
                                            (select round(toplamKelimeOyunuSayisi/ DATEDIFF(CURDATE(), 'GAME_START_DATE'),2)) as gunlukOrtalamaKelimeOyunuSayisi,
                                            (select round(toplamIslemOyunuSayisi / toplamOyuncuSayisi,2)) as kullaniciBasinaIslemOyunSayisi,
                                            (select round(toplamKelimeOyunuSayisi / toplamOyuncuSayisi,2)) as kullaniciBasinaKelimeOyunSayisi,
                                            (select round((toplamIslemOyunuSayisi + toplamKelimeOyunuSayisi) / toplamOyuncuSayisi,2)) as kullaniciBasinaOyunSayisi";

    public static $QUERY_BEN = "select * from 
                                (select (tamSonuc+yaklasik1+yaklasik2+yaklasik3+yaklasik4+yaklasik5+islemSifirPuan) toplamIslemOyunSayisi, islemOzet.* from
                                        (select ifnull(group_concat(if(yaklasik=0,sayi, NULL)),0) AS  tamSonuc,
                                                ifnull(group_concat(if(yaklasik=1,sayi, NULL)),0) AS  yaklasik1,
                                                ifnull(group_concat(if(yaklasik=2,sayi, NULL)),0) AS  yaklasik2,
                                                ifnull(group_concat(if(yaklasik=3,sayi, NULL)),0) AS  yaklasik3,
                                                ifnull(group_concat(if(yaklasik=4,sayi, NULL)),0) AS  yaklasik4,
                                                ifnull(group_concat(if(yaklasik=5,sayi, NULL)),0) AS  yaklasik5,
                                                islemSifirPuan
                                          from (select abs(t.targetnumber-t.userresult) as yaklasik, count(*) sayi from answersnumbers t where points > 0 and userresult > 0 and userid='USER_ID' group by yaklasik) yaklasiklar,
                                                  (select count(*) as islemSifirPuan from answersnumbers t where points = 0 and userid='USER_ID') as islemSifirPuan) islemOzet) as islem,
                                (select (harf3 + harf4 + harf5 + harf6 + harf7 + harf8 + harf9 + harf10 + harf11 + kelimeSifirPuan) as toplamKelimeOyunuSayisi, kelimeOzet.* from (select
                                        ifnull(group_concat(if(kelimeuzunlugu=3,sayi, NULL)),0) AS  harf3,
                                        ifnull(group_concat(if(kelimeuzunlugu=4,sayi, NULL)),0) AS  harf4,
                                        ifnull(group_concat(if(kelimeuzunlugu=5,sayi, NULL)),0) AS  harf5,
                                        ifnull(group_concat(if(kelimeuzunlugu=6,sayi, NULL)),0) AS  harf6,
                                        ifnull(group_concat(if(kelimeuzunlugu=7,sayi, NULL)),0) AS  harf7,
                                        ifnull(group_concat(if(kelimeuzunlugu=8,sayi, NULL)),0) AS  harf8,
                                        ifnull(group_concat(if(kelimeuzunlugu=9,sayi, NULL)),0) AS  harf9,
                                        ifnull(group_concat(if(kelimeuzunlugu=10,sayi, NULL)),0) AS  harf10,
                                        ifnull(group_concat(if(kelimeuzunlugu=11,sayi, NULL)),0) AS  harf11,
                                        kelimeSifirPuan
                                  from (select length(answer) as kelimeuzunlugu, count(*) sayi from answerswords t where points > 0  and userid='USER_ID' group by kelimeuzunlugu) kelimeUzunlugu,
                                          (select count(*) as kelimeSifirPuan from answerswords t where points = 0 and userid='USER_ID') as kelimeSifirPuan) kelimeOzet) as kelime,
                                (select  (select date_format(createdate, '%d/%m/%Y - %H:%i:%s') from users where id='USER_ID') as kayitTarihi,
                                        (select (count(*)) from answersnumbers where userid='USER_ID') as toplamIslemOyunuSayisi,
                                        (select (count(*)) from answerswords where userid='USER_ID') as toplamKelimeOyunuSayisi,
                                        (select round(toplamIslemOyunuSayisi/ DATEDIFF(CURDATE(), '2013-01-01'), 2)) as gunlukOrtalamaIslemOyunuSayisi,
                                        (select round(toplamKelimeOyunuSayisi/ DATEDIFF(CURDATE(), '2013-01-01'),2)) as gunlukOrtalamaKelimeOyunuSayisi,
                                        (select round((100*count(a.id) /toplamIslemOyunuSayisi), 2) from answersnumbers a, users u where u.id='USER_ID' and u.id = a.userid and a.points>0) as islemPuanAlmaYuzdesi,
                                        (select round((100*count(a.id) /toplamIslemOyunuSayisi) ,2) from answersnumbers a, users u where u.id='USER_ID' and u.id = a.userid and a.points>0 and a.userresult = a.targetnumber) as islemTamSonucaUlasmaYuzdesi,
                                        (select round(avg(remainingtime),2) from answersnumbers a, users u where u.id='USER_ID' and  u.id = a.userid and points > 0) as islemOrtalamaKalanSure,
                                        (select round(avg(remainingtime),2) from answersnumbers a, users u where u.id='USER_ID' and  u.id = a.userid and points > 0 and targetnumber=userresult) as islemTamSonucOrtalamaKalanSure,
                                        (select sum(points) from answersnumbers a, users u where u.id='USER_ID' and  u.id = a.userid) as islemToplamPuan,
                                        (select round(avg(points),2) from answersnumbers a, users u where u.id='USER_ID' and  u.id = a.userid) as islemOrtalamaPuan,
                                        (select sum(points) from answerswords a, users u where u.id='USER_ID' and  u.id = a.userid) as kelimeToplamPuan,
                                        (select round(avg(points),2) from answerswords a, users u where u.id='USER_ID' and  u.id = a.userid) as kelimeOrtalamaPuan,
                                        (select count(a.id) from answerswords a, users u where u.id='USER_ID' and u.id=a.userid and a.points > 0 and ((length(a.answer) = 8 and a.answer not like '%?%') or length(a.answer) > 8)) as kelimeTamSonucaUlasmaSayisi,
                                        (select round(count(a.id)/toplamKelimeOyunuSayisi,2) from answerswords a, users u where u.id='USER_ID' and u.id=a.userid and a.points > 0 and ((length(a.answer) = 8 and a.answer not like '%?%') or length(a.answer) > 8)) as kelimeTamSonucaUlasmaYuzdesi,
                                        (select round((100*count(a.id) /toplamKelimeOyunuSayisi), 2) from answerswords a, users u where u.id='USER_ID' and u.id=a.userid and u.id = a.userid and a.points>0) as kelimeSorudanPuanAlmaYuzdesi,
                                        (select round(avg(remainingtime),2) from answerswords a, users u where u.id='USER_ID' and  u.id = a.userid and points > 0) as kelimeOrtalamaKalanSure,
                                        (select round(avg(length(a.answer)), 2) from answerswords a, users u where u.id='USER_ID' and u.id = a.userid and a.points>0) as kelimeOrtalamaHarfSayisi) as ortalama";

    
    
    public static function init(){
        self::prepareStatsArray();
    }

    public static function prepareStatsArray(){



    }




}

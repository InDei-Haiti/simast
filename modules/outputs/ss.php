
<?
$buffer="<td data-text='Breakfast, Mid-morning, Lunch, Mid-afternoon, Supper. Ferta' class='vcell moreview  text-left'>Breakfast, Mid-morning. Lunch, Mid-afternoon...</td><td data-text='Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old' class='vcell moreview  text-left'>Cont. rar. y to p. ..o...pular...</td>";
preg_match_all("/data-text='([^']*)'[^>]*>([^<]*\.{3})<\/td>/",$buffer,$rtext);
var_dump($rtext);
if(count($rtext[2] ) > 0) {
    foreach($rtext[2] as $key => $val){
	$buffer=str_replace($val,$rtext[1][$key],$buffer);
    }
}
echo $buffer;
?>
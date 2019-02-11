<?
class MailSender
{
	function send($fromName, $toAddress, $toName, $subject, $contents)
	{

		
		$fromAddress = $_SERVER["HTTP_HOST"]=='cds.jeycorp.com'?'cartax@naver.com':'system@cartax.biz';
		
		$toName = iconv("utf-8", "euc-kr", $toName);
		$fromName = iconv("utf-8", "euc-kr", $fromName);
		$subject = iconv("utf-8", "euc-kr", $subject);
		$contents = iconv("utf-8", "euc-kr", $contents);


		$mailheaders .= "Return-Path: <". $fromAddress. ">\n";
		$mailheaders .= "From: ". $fromName. " <". $fromAddress. ">\n";
		$mailheaders .= "X-Sender: <". $fromAddress. ">\n";
		$mailheaders .= "X-Mailer: PHP\n";
		$mailheaders .= "Reply-To: ". $fromName. " <". $fromAddress. ">\n";
		$mailheaders .= "MIME-Version: 1.0\n";
		$mailheaders .= "Content-Type: text/html;\n\tcharset=euc-kr";


		$to = $toName. " <". $toAddress. ">";

		mail($to, $subject, $contents, $mailheaders);
	}
	
	function send_mail_with_file($from_email,$from_name,$to_email,$subject,$body,$file)
	{ 
		if (strlen($to_email)==0) return 0; 
		$mailheaders .= "From: $from_name<$from_email> \r\n"; 
		$mailheaders .= "Reply-To: $from_name<$from_email>\r\n"; 
		$mailheaders .= "Return-Path: $from_name<$from_email>\r\n"; 
		// if ($file[size]>0) { 
// 			$boundary = uniqid("part"); 
// 			if (strlen($file[type])==0) $file[type] = "application/octet-stream"; 
// 
// 			$mailheaders .= "MIME-Version: 1.0\r\n"; 
// 			$mailheaders .= "Content-Type: Multipart/mixed; boundary = \"".$boundary."\""; 
// 
// 			$bodytext = "This is a multi-part message in MIME format.\r\n\r\n"; 
// 			$bodytext .= "--".$boundary."\r\n"; 
// 			$bodytext .= "Content-Type: text/html; charset=\"utf-8\"\r\n"; 
// 			$bodytext .= "Content-Transfer-Encoding: base64\r\n\r\n"; 
// 			$bodytext .= chunk_split(base64_encode($body))."\r\n\r\n"; 
// 
// 			$bodytext .= "--".$boundary."\r\n"; 
// 			$bodytext .= "Content-Type: ".$file[type]."; name=\"".$file[name]."\"\r\n"; 
// 			$bodytext .= "Content-Transfer-Encoding: base64\r\n"; 
// 			$bodytext .= "Content-Disposition: attachment; filename=\"".$file[name]."\"\r\n\r\n"; 
// 			$bodytext .= chunk_split(base64_encode($file[data]))."\r\n\r\n"; 
// 
// 			$bodytext .= "--".$boundary."--"; 
// 		} else { 
			$mailheaders .= "Content-Type: text/html \r\n\r\n"; 
			$bodytext = $body . "\r\n\r\n"; 
// 		} 
		if(!mail($to_email,$subject,$bodytext,$mailheaders)) {return 0;} 
		return 1; 
	} 
	
	function sendTest(){
	
		$fromAddress = $_SERVER["HTTP_HOST"]=='cds.jeycorp.com'?'cartax@naver.com':'system@cartax.biz';
		
		$toName = iconv("utf-8", "euc-kr", $toName);
		$fromName = iconv("utf-8", "euc-kr", $fromName);
		$subject = iconv("utf-8", "euc-kr", $subject);
		$contents = iconv("utf-8", "euc-kr", $contents);


		$mailheaders .= "Return-Path: <". $fromAddress. ">\n";
		$mailheaders .= "From: ". $fromName. " <". $fromAddress. ">\n";
		$mailheaders .= "X-Sender: <". $fromAddress. ">\n";
		$mailheaders .= "X-Mailer: PHP\n";
		$mailheaders .= "Reply-To: ". $fromName. " <". $fromAddress. ">\n";
		$mailheaders .= "MIME-Version: 1.0\n";
		$mailheaders .= "Content-Type: text/html;\n\tcharset=euc-kr";


		$to = $toName. " <". $toAddress. ">";

		mail($to, $subject, $contents, $mailheaders);
	}
	
	function sendTempPassword($employeeNumber,$password){
		
		send("카택스_신세계","kkokkokim14@naver.com","kkokkokim14님","카택스_신세계 임지 비밀번호 발급",$password);
	}

	//회원 가입시
	function joinContents($adminName,$cid,$licenceQuantity,$paymentTerm,$estimateResult)
	{

		$daily = array('일','월','화','수','목','금','토');
		$yo = date('w'); 
		$today = date("Y년 m월 d일 ") ;

// 		$subject = '카택스 회원가입 해주셔서 대단히 감사합니다.';

		$message = '<table width="708" cellspacing="0" cellpadding="0" style="border:1px solid #eee; letter-spacing:-0.05em;">
			<tbody>
				<tr>
					<td style="padding:20px;">
						<table width="708" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:20px;">
							<tbody>
								<tr>
									<td align="left" height="60"><a href="http://cartax.oil" target="_blank" title="카택스 홈페이지 바로가기"><img src="http://cds.carbeast.co.kr/oil/images/email/lgo_emal_top.png" width="144" height="34" border="0" alt="카택스 홈페이지 바로가기" /></a></td>
									<td align="right" valign="bottom">
										<!-- 발송일 -->
										<span style="display:block; padding-bottom:5px; font-size:12px; font-weight:bold; color:#444;">발송일 : ' .$today . $daily[$yo] .  '</span>
										<!-- // 발송일 -->
									</td>
								</tr>
								<tr>
									<td colspan="2" height="2" bgcolor="#ffa800"></td>
								</tr>
							</tbody>
						</table>
						<table width="708" border="0" cellspacing="0" cellpadding="0" bgcolor="#3d4456" style="margin-bottom:20px;">
							<tbody>
								<tr>
									<td align="center" width="220" style="background:url(http://cds.carbeast.co.kr/oil/images/email/bg_email_top.png) no-repeat 50% 50%;"></td>
									<td align="left" style="padding:25px 0;">
										<span style="display:block; padding-bottom:15px;"><img src="http://cds.carbeast.co.kr/oil/images/email/img_emal_txt1.png" width="389" height="25" border="0" /></span>
										<span style="display:block; padding-bottom:15px; font-size:12px; color:#fff;">
											안녕하세요, <strong style="color:#ffcc00;">' . $adminName. '</strong>회원님.
										</span>
										<span style="display:block; padding-bottom:3px; font-size:12px; color:#fff;">카택스에 가입해 주셔서 감사합니다.</span>
										<span style="display:block; padding-bottom:15px; font-size:12px; color:#fff;">편안하게 서비스 이용이 될 수 있게 최선을 다하겠습니다.</span>
										<span style="display:block; padding-bottom:3px; font-size:12px; color:#fff;">회원님의 정보는 안전하게 보호되며,</span>
										<span style="display:block; padding-bottom:3px; font-size:12px; color:#fff;">정보변경은 <strong style="color:#ffcc00;">관리자페이지 > 설정 > 계정 설정</strong>에서 가능합니다.</span>
									</td>
								</tr>
							</tbody>
						</table>
						<b style="border-left:5px solid #ffcc00; padding-left:5px; margin-bottom:10px; font-size:14px; color:#444;">가입정보</b>

						<table width="708" border="0" cellspacing="0" cellpadding="0" style="margin:10px 0; border-top:2px solid #888; border-bottom:1px solid #ddd;">
							<tbody>
								<tr>
									<th align="left" width="180" height="50" style="background:#eee;"><b style="font-size:14px; color:#444; padding:0 15px;">서비스</b></th>
									<!-- 가입 서비스 -->
									<td align="left" style="border-top:1px solid #ddd;"><span style="font-size:14px; color:#444; padding:0 15px;">카택스오일</span></td>
									<!-- // 가입 서비스 -->
								</tr>
								<tr>
									<th align="left" width="180" height="50" style="border-top:1px solid #ddd; background:#eee;"><b style="font-size:14px; color:#444; padding:0 15px;">담당자 이름</b></th>
									<!-- 담당자 이름 -->
									<td align="left" style="border-top:1px solid #ddd;"><span style="font-size:14px; color:#444; padding:0 15px;">' . $adminName. '</span></td>
									<!-- // 담당자 이름 -->
								</tr>
								<tr>
									<th align="left" width="180" height="50" style="border-top:1px solid #ddd; background:#eee;"><b style="font-size:14px; color:#444; padding:0 15px;">ID</b></th>
									<!-- ID -->
									<td align="left" style="border-top:1px solid #ddd;"><span style="font-size:14px; color:#444; padding:0 15px;">' .$cid. '</span></td>
									<!-- // ID -->
								</tr>
								<tr>
									<th align="left" width="180" height="50" style="border-top:1px solid #ddd; background:#eee;"><b style="font-size:14px; color:#444; padding:0 15px;">가입일</b></th>
									<!-- 가입일 -->
									<td align="left" style="border-top:1px solid #ddd;"><span style="font-size:14px; color:#444; padding:0 15px;">' . $today .'</span></td>
									<!-- // 가입일 -->
								</tr>
							</tbody>
						</table>
						<img src="http://cds.carbeast.co.kr/oil/images/email/ban_email_good.png" alt="카택스만의 혜택(혜택 1.조직운영 가능, 혜택 2. 주유대 관리 가능, 혜택 3. 자동운행 기록, 혜택 4. 국세청 증빙자료 가능, 혜택 5. 추가비용 X, 혜택 6. 웹 통합 관리자 제공)" style="padding-top:10px;" />
					</td>
				</tr>
				<tr>
					<td style="background:#f9f9f9; padding:20px; color:#444; font-size:12px;">
						<span style="display:block; padding-bottom:2px;">궁금하신 부분은 언제든지 <a href="mailto:carbeast77@gmail.com" style="color:#444; font-weight:bold;">이메일(carbeast77@gmail.com)</a>이나,</span>
						<span style="display:block; padding-bottom:12px; font-size:12px;"><a href="tel:07085854799" style="color:#444; font-weight:bold;">고객센터(070-8785-4799)</a>로 문의해 주시면 신속하게 답변 드리겠습니다.</span>
						<span style="display:block; padding-bottom:2px; font-size:12px;">대표 : 안재희 | 사업자등록번호 : 514-24-89820 | 통신판매업신고번호 : 제 2011-대구남구-0298호 | 위치기반 사업자신고 번호 : 제1008호</span>
						<span style="display:block; font-size:12px;">주소 : 대구광역시 남구 현충로 170 YNC천마스퀘어 806호 제이코프 | 대표전화 : <a href="tel:07085854799" style="color:#444; font-weight:bold;">070-8785-4799</a> | 이메일 : <a href="mailto:carbeast77@gmail.com" style="color:#444; font-weight:bold;">carbeast77@gmail.com</a></span>
					</td>
				</tr>
			</tbody>
		</table>';
		return $message;
	}
	
	//비밀번호 찾기시
	function passwordContents($pwd,$cid,$adminName){


		$daily = array('일','월','화','수','목','금','토');
		$yo = date('w'); 
		$today = date("Y년 m월 d일 ") ;
		//$subject = '카택스 비밀번호 초기화';

		$message = '<table width="708" cellspacing="0" cellpadding="0" style="border:1px solid #eee; letter-spacing:-0.05em;">
			<tbody>
				<tr>
					<td style="padding:20px;">
						<table width="708" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:20px;">
							<tbody>
								<tr>
									<td align="left" height="60"><a href="http://cartax.oil" target="_blank" title="카택스 홈페이지 바로가기"><img src="http://cds.carbeast.co.kr/oil/images/email/lgo_emal_top.png" width="144" height="34" border="0" alt="카택스 홈페이지 바로가기" /></a></td>
									<td align="right" valign="bottom">
										<!-- 발송일 -->
										<span style="display:block; padding-bottom:5px; font-size:12px; font-weight:bold; color:#444;">발송일 :' . $today . $daily[$yo]  .  '</span>
										<!-- // 발송일 -->
									</td>
								</tr>
								<tr>
									<td colspan="2" height="2" bgcolor="#ffa800"></td>
								</tr>
							</tbody>
						</table>
						<table width="708" border="0" cellspacing="0" cellpadding="0" bgcolor="#3d4456" style="margin-bottom:20px;">
							<tbody>
								<tr>
									<td align="center" width="220" style="background:url(http://cds.carbeast.co.kr/oil/images/email/bg_email_top.png) no-repeat 50% 50%;"></td>
									<td align="left" style="padding:25px 0;">
										<span style="display:block; padding-bottom:15px;"><img src="http://cds.carbeast.co.kr/oil/images/email/img_emal_txt3.png" width="389" height="25" border="0" /></span>
										<span style="display:block; padding-bottom:15px; font-size:12px; color:#fff;">
											<!-- 담당자명[D] 이름 암호화 선택 -->
											안녕하세요, <strong style="color:#ffcc00;">' .$adminName. '</strong>회원님.
											<!-- // 담당자명 -->
										</span>
										<span style="display:block; padding-bottom:3px; font-size:12px; color:#fff;">회원님의 카택스계정 <strong style="color:#ffcc00;">'.$adminName.'</strong>에 대한 비밀번호 초기화 요청을 접수 했습니다.</span>
										<span style="display:block; padding-bottom:3px; font-size:12px; color:#fff;">원하는 비밀번호로 변경하고 싶으시다면,</span>
										<span style="display:block; padding-bottom:3px; font-size:12px; color:#fff;"><strong style="color:#ffcc00;">관리자페이지 > 설정 > 계정 설정</strong>에서 비밀번호 설정이 가능합니다.</span>
									</td>
								</tr>
							</tbody>
						</table>
						<b style="border-left:5px solid #ffcc00; padding-left:5px; margin-bottom:10px; font-size:14px; color:#444;">계정접속정보</b>
				
						<table width="708" border="0" cellspacing="0" cellpadding="0" style="margin:10px 0; border-top:2px solid #888; border-bottom:1px solid #ddd;">
							<tbody>
								<tr>
									<th align="left" width="180" height="50" style="background:#eee;"><b style="font-size:14px; color:#444; padding:0 15px;">서비스</b></th>
									<!-- 가입 서비스 -->
									<td align="left" style="border-top:1px solid #ddd;"><span style="font-size:14px; color:#444; padding:0 15px;">카택스오일</span></td>
									<!-- // 가입 서비스 -->
								</tr>
								<tr>
									<th align="left" width="180" height="50" style="border-top:1px solid #ddd; background:#eee;"><b style="font-size:14px; color:#444; padding:0 15px;">담당자 이름</b></th>
									<!-- 담당자 이름 -->
									<td align="left" style="border-top:1px solid #ddd;"><span style="font-size:14px; color:#444; padding:0 15px;">' .$adminName. '</span></td>
									<!-- // 담당자 이름 -->
								</tr>
								<tr>
									<th align="left" width="180" height="50" style="border-top:1px solid #ddd; background:#eee;"><b style="font-size:14px; color:#444; padding:0 15px;">ID</b></th>
									<!-- ID -->
									<td align="left" style="border-top:1px solid #ddd;"><span style="font-size:14px; color:#444; padding:0 15px;">'.$cid.'</span></td>
									<!-- // ID -->
								</tr>
								<tr>
									<th align="left" width="180" height="50" style="border-top:1px solid #ddd; background:#eee;"><b style="font-size:14px; color:#444; padding:0 15px;">비밀번호</b></th>
									<!-- ID -->
									<td align="left" style="border-top:1px solid #ddd;"><span style="font-size:14px; color:#444; padding:0 15px;">'.$pwd.'</span></td>
									<!-- // ID -->
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td style="background:#f9f9f9; padding:20px; color:#444; font-size:12px;">
						<span style="display:block; padding-bottom:2px;">궁금하신 부분은 언제든지 <a href="mailto:carbeast77@gmail.com" style="color:#444; font-weight:bold;">이메일(carbeast77@gmail.com)</a>이나,</span>
						<span style="display:block; padding-bottom:12px; font-size:12px;"><a href="tel:07085854799" style="color:#444; font-weight:bold;">고객센터(070-8785-4799)</a>로 문의해 주시면 신속하게 답변 드리겠습니다.</span>
						<span style="display:block; padding-bottom:2px; font-size:12px;">대표 : 안재희 | 사업자등록번호 : 514-24-89820 | 통신판매업신고번호 : 제 2011-대구남구-0298호 | 위치기반 사업자신고 번호 : 제1008호</span>
						<span style="display:block; font-size:12px;">주소 : 대구광역시 남구 현충로 170 YNC천마스퀘어 806호 제이코프 | 대표전화 : <a href="tel:07085854799" style="color:#444; font-weight:bold;">070-8785-4799</a> | 이메일 : <a href="mailto:carbeast77@gmail.com" style="color:#444; font-weight:bold;">carbeast77@gmail.com</a></span>
					</td>
				</tr>
			</tbody>
		</table>';
		return $message;
	}
	
	//계약이 만료되어 갈때
	function contractEmail($cid,$name,$paymentTerm,$licenceQuantity,$beginDate,$endDate){
	
		$daily = array('일','월','화','수','목','금','토');
		$yo = date('w'); 
		$today = date("Y년 m월 d일 ") ;
	
		$message = '<table width="708" cellspacing="0" cellpadding="0" style="border:1px solid #eee; letter-spacing:-0.05em;">
					<tbody>
						<tr>
							<td style="padding:20px;">
								<table width="708" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:20px;">
									<tbody>
										<tr>
											<td align="left" height="60"><a href="http://cartax.oil" target="_blank" title="카택스 홈페이지 바로가기"><img src="http://cds.carbeast.co.kr/oil/images/email/lgo_emal_top.png" width="144" height="34" border="0" alt="카택스 홈페이지 바로가기" /></a></td>
											<td align="right" valign="bottom">
												<!-- 발송일 -->
												<span style="display:block; padding-bottom:5px; font-size:12px; font-weight:bold; color:#444;">발송일 : ' .$today. $daily[yo] .'</span>
												<!-- // 발송일 -->
											</td>
										</tr>
										<tr>
											<td colspan="2" height="2" bgcolor="#ffa800"></td>
										</tr>
									</tbody>
								</table>
								<table width="708" border="0" cellspacing="0" cellpadding="0" bgcolor="#3d4456" style="margin-bottom:20px;">
									<tbody>
										<tr>
											<td align="center" width="220" style="background:url(http://cds.carbeast.co.kr/oil/images/email/bg_email_top.png) no-repeat 50% 50%;"></td>
											<td align="left" style="padding:25px 0;">
												<span style="display:block; padding-bottom:15px;"><img src="http://cds.carbeast.co.kr/oil/images/email/img_emal_txt2.png" width="367" height="25" border="0" /></span>
												<span style="display:block; padding-bottom:15px; font-size:12px; color:#fff;">
	
													안녕하세요, <strong style="color:#ffcc00;">' . $name .'</strong>회원님.
	
												</span>
												<!-- 서비스 만료일자 -->
												<span style="display:block; padding-bottom:3px; font-size:12px; color:#fff;"><strong style="color:#ffcc00;">'.$endDate.'</strong> 부로 서비스 기간이 만료될 예정입니다.</span>
												<!-- // 서비스 만료일자 -->
												<span style="display:block; padding-bottom:15px; font-size:12px; color:#fff;"><strong style="color:#ffcc00;">서비스 기간 만료</strong> 이후 계정 접속이 제한될수 있지만,</span>
												<span style="display:block; padding-bottom:3px; font-size:12px; color:#fff;"><strong style="color:#ffcc00;">저장된 데이터는 6개월 간 보관</strong>되며, <strong style="color:#ffcc00;">연장 시 복원</strong>하여 사용할수 있습니다.</span>
												<span style="display:block; padding-bottom:3px; font-size:12px; color:#fff;">빠른 연장계약으로, 서비스 이용에 차질이 없으시길 바랍니다.</span>
											</td>
										</tr>
									</tbody>
								</table>
								<b style="border-left:5px solid #ffcc00; padding-left:5px; margin-bottom:10px; font-size:14px; color:#444;">이용계정정보</b>
				
								<table width="708" border="0" cellspacing="0" cellpadding="0" style="margin:10px 0 20px; border-top:2px solid #888; border-bottom:1px solid #ddd;">
									<tbody>
										<tr>
											<th align="left" width="180" height="50" style="background:#eee;"><b style="font-size:14px; color:#444; padding:0 15px;">서비스</b></th>
											<!-- 가입 서비스 -->
											<td align="left" style="border-top:1px solid #ddd;"><span style="font-size:14px; color:#444; padding:0 15px;">카택스오일</span></td>
											<!-- // 가입 서비스 -->
										</tr>
										<tr>
											<th align="left" width="180" height="50" style="border-top:1px solid #ddd; background:#eee;"><b style="font-size:14px; color:#444; padding:0 15px;">ID</b></th>
											<!-- ID -->
											<td align="left" style="border-top:1px solid #ddd;"><span style="font-size:14px; color:#444; padding:0 15px;">'.$cid.'</span></td>
											<!-- // ID -->
										</tr>
										<tr>
											<th align="left" width="180" height="50" style="border-top:1px solid #ddd; background:#eee;"><b style="font-size:14px; color:#444; padding:0 15px;">차량 운영 대수</b></th>
											<!-- 차량 운영 대수 -->
											<td align="left" style="border-top:1px solid #ddd;"><span style="font-size:14px; color:#444; padding:0 15px;">'.$licenceQuantity.'대</span></td>
											<!-- // 차량 운영 대수 -->
										</tr>
										<tr>
											<th align="left" width="180" height="50" style="border-top:1px solid #ddd; background:#eee;"><b style="font-size:14px; color:#444; padding:0 15px;">계약기간</b></th>
											<!-- 계약기간 -->
											<td align="left" style="border-top:1px solid #ddd; font-size:12px;"><strong style="font-size:14px; color:#3ea3dc; padding:0 0 0 15px;">'.$paymentTerm.'개월</strong> ('.$beginDate. ' ~ '.$endDate. ')</td>
											<!-- // 계약기간 -->
										</tr>
									</tbody>
								</table>
								<!-- 연장 안내 버튼 -->
								<a href="https://cds.carbeast.co.kr/oil/" target="_blank" title="카택스서비스 연장 안내 버튼">
									<img src="http://cds.carbeast.co.kr/oil/images/email/ban_email_plus.png" alt="카택스서비스 연장 안내 버튼" />
								</a>
							</td>
						</tr>
						<tr>
							<td style="background:#f9f9f9; padding:20px; color:#444; font-size:12px;">
								<span style="display:block; padding-bottom:2px;">궁금하신 부분은 언제든지 <a href="mailto:carbeast77@gmail.com" style="color:#444; font-weight:bold;">이메일(carbeast77@gmail.com)</a>이나,</span>
								<span style="display:block; padding-bottom:12px; font-size:12px;"><a href="tel:07085854799" style="color:#444; font-weight:bold;">고객센터(070-8785-4799)</a>로 문의해 주시면 신속하게 답변 드리겠습니다.</span>
								<span style="display:block; padding-bottom:2px; font-size:12px;">대표 : 안재희 | 사업자등록번호 : 696-86-00649 | 통신판매업신고번호 : 제 2011-대구남구-0298호 | 위치기반 사업자신고 번호 : 제1008호</span>
								<span style="display:block; font-size:12px;">주소 : 대구광역시 남구 현충로 170, 304호(대명동, 영남이공대학 산학협력관) (주)카택스 | 대표전화 : <a href="tel:07085854799" style="color:#444; font-weight:bold;">070-8785-4799</a> | 이메일 : <a href="mailto:carbeast77@gmail.com" style="color:#444; font-weight:bold;">carbeast77@gmail.com</a></span>
							</td>
						</tr>
					</tbody>
				</table>';
				return $message;
	}


}
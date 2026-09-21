<?php
function complaintLogLateNoticeAgency($path, $id, $reportName, $code_name, $userType, $userReportName, $outputName, $reportDescription, $mailNotification, $sftpId, $mode, $run_by, $reportBasePath)
{
    $report_start_time = date("H:i:s");
    $date = new DateTime();
    $codeNames = explode(",", $code_name);
    $dataPresents = array();
    $noDataPresents = array();
    $new_status = 3;
    $status_msg = 'Failed';
    $status = 0;
    $folder = explode(",", $path);
    $distFolder = getDisFolder($folder[0], $reportBasePath);
    $distinationFolder = getFolderName($distFolder);

    $query = "SELECT DISTINCT  B.CURR_ATTY_CD
		FROM CMPLOGNR A
		INNER JOIN HSFLCLNTWF B 
		ON A.FIRMCD = B.CURR_ATTY_CD
		WHERE B.HACL = 0
		AND B.CURR_STS_CD NOT IN ('12R','123','122','12E','12S','120','12C')
		AND B.CURR_ATTY_CD <> ''
		ORDER BY B.CURR_ATTY_NME";
    $results = getResult($query);
    if ($results['numRows'] > 0) {

        foreach ($results['results'] as $result) {
            if (in_array($result['CURR_ATTY_CD'], $codeNames)) {

                $emailid = array();
                $emailid = getEmailId($result['CURR_ATTY_CD'], $userType, $distinationFolder, $path = '');
                if ($emailid) {
                    foreach ($emailid as $email) {
                        Sendmail17($email);
                    }
                    array_push($dataPresents, array(
                        'clientcode' => $result['CURR_ATTY_CD']
                    ));
                }
            } else {
                array_push($noDataPresents, array(
                    'clientcode' => $result['CURR_ATTY_CD']
                ));
            }
        }
    }
    if (!empty($dataPresents)) {
        $new_status = 2;
        $status_msg = 'generated';
        $status = 1;
    } else {
        $new_status = 3;
        $status_msg = 'Failed';
        $status = 0;
    }
    return array('status' => $status, 'status_msg' => $status_msg, 'new_status' => $new_status);
}

function Sendmail17($newid)
{
    global $mail;
    $mail->clearAddresses();
    $mail->addAddress($newid);
    $mail->Subject = 'Complaint Log Late Notice Agency';
    $mail->Body = "<p>We failed to receive a complaint log from your Agency for the preceding month.  Complaint logs are due on the 1st business day of the following month.  AACANet requires that firms submit the log even if the firm did not have any consumer complaints to report.  If you are experiencing difficulty with submitting the log, please contact <a href='mailto:Compliance@aacanet.org'>Compliance@aacanet.org</a>. Otherwise please submit the log for last month.  Thank you</p>";
    if (!$mail->send()) {

        echo "Email not sent. ", $mail->ErrorInfo, PHP_EOL;
    }
}

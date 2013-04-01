			<?php
            $psettings = array();

            foreach ($ticket['PaymentDetail'] as $v):
                $psettings[0] = $v['userPaymentSettingId'];
                ?>
				<table class="paymentsApplied">
					<tr>
						<td>Payment ID:</td>
                        <!-- the class below is important, it is used to select the credit card. please do not change -->
						<td><a target="_blank" href="/payment_details/view/<?= $v['paymentDetailId'] ?>" class="<?= $v['userPaymentSettingId'] ?>"><?= $v['paymentDetailId'] ?></a></td>
					</tr>
					<tr>
						<td>Payment Status:</td>
						<td><? if ($v['isSuccessfulCharge'] == 1): ?><span style="color: #00ff00">APPROVED</span><? else: ?><span style="color: #ff0000">DECLINED</span><? endif; ?></td>
					</tr>					
					<tr>
						<td>Type:</td>
						<td><?= $paymentTypeIds[$v['paymentTypeId']] ?></td>
					</tr>
					<tr>
						<td>Amount:</td>
						<td>$<?= number_format($v['paymentAmount'],2) ?> </td>
					</tr>
				</table>
			<?php endforeach; ?>


            <script type="text/javascript">

                (function($){


                    var userPaymentSettingId = <?= $psettings[0] ?>;// last payment setting

                    //grab selector object of lasst used payment setting ID
                    var lastUsedPaymentSel = $('input[value="'+userPaymentSettingId+'"]');

                    //ensure the selector exists.
                    if (lastUsedPaymentSel.length > 0){

                        //check select the radio button for this object.
                        $('input[value="'+userPaymentSettingId+'"]').attr("checked",true);
                     }
                    //
                })(jQuery);

            </script>
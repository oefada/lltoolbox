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
						<td><?=$currencySymbol?><?php if ($useTldCurrency): ?><?= number_format($v['paymentAmountTld'],2) ?><?php else: ?><?= number_format($v['paymentAmount'],2) ?><?php endif; ?> </td>
					</tr>
				</table>
			<?php endforeach; ?>


            <script type="text/javascript">
                (function($){
                    var userPaymentSettingId = <?= isset($psettings[0]) ? $psettings[0] : 0 ?>;
                    var lastUsedPaymentSel = $('input[value="'+userPaymentSettingId+'"]');

                    if (lastUsedPaymentSel.length > 0){
                        $('input[value="'+userPaymentSettingId+'"]').attr("checked",true);
                     }
                })(jQuery);
            </script>

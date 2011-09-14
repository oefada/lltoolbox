			<?php foreach ($ticket['PaymentDetail'] as $v): ?>
				<table class="paymentsApplied">
					<tr>
						<td>Payment ID:</td>
						<td><a target="_blank" href="/payment_details/view/<?= $v['paymentDetailId'] ?>"><?= $v['paymentDetailId'] ?></a></td>
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

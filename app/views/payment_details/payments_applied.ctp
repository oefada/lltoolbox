			<?php foreach ($ticket['PaymentDetail'] as $v): ?>
				<table class="paymentsApplied">
					<tr>
						<td>Payment ID:</td>
						<td><a target="_blank" href="/payment_details/view/<?= $v['paymentDetailId'] ?>"><?= $v['paymentDetailId'] ?></a></td>
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

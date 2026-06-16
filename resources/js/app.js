import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Provide a global factory so `x-data="specialtyManager()"` always resolves
window.specialtyManager = function() {
	return {
		showAddDoctor: false,
		showAddRoom: false,
		successMessage: '',
		deleteInProgress: false,
		async deleteHandler(url, message, id, type) {
			const confirmMsg = type === 'doctor'
				? 'Xác nhận xóa bác sĩ khỏi chuyên khoa này?'
				: 'Xác nhận xóa phòng khỏi chuyên khoa này?';

			if (!confirm(confirmMsg)) return;

			this.deleteInProgress = true;
			try {
				const token = document.querySelector('input[name="_token"]')?.value;
				await fetch(url, {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': token,
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(type === 'doctor' ? { doctor_id: id } : { room_id: id })
				});
				this.successMessage = message;
				setTimeout(() => location.reload(), 1000);
			} catch (e) {
				alert('Có lỗi xảy ra');
			} finally {
				this.deleteInProgress = false;
			}
		}
	};
};

Alpine.start();

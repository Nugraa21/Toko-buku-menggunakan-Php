<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = $_POST['id'];

        // Get book_id before deleting to update rating later
        $get_book = $pdo->prepare("SELECT book_id FROM reviews WHERE id = ?");
        $get_book->execute([$id]);
        $book_id = $get_book->fetchColumn();

        if ($book_id) {
            $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$id]);

            // Recalculate rating
            updateBookRating($pdo, $book_id);

            $_SESSION['flash_message'] = "Ulasan berhasil dihapus.";
            $_SESSION['flash_type'] = "success";
        }
        redirect('index.php?page=admin_reviews');
    }

    if ($action === 'add' || $action === 'edit') {
        $book_id = $_POST['book_id'];
        $rating = intval($_POST['rating']);
        $comment = trim($_POST['comment']);

        if ($action === 'add') {
            // Admin adds review as themselves
            $user_id = $_SESSION['user_id'];

            // Check if already reviewed
            $check = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND book_id = ?");
            $check->execute([$user_id, $book_id]);
            if ($check->rowCount() > 0) {
                $_SESSION['flash_message'] = "Anda sudah mengulas buku ini.";
                $_SESSION['flash_type'] = "error";
            } else {
                $stmt = $pdo->prepare("INSERT INTO reviews (user_id, book_id, rating, comment) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $book_id, $rating, $comment]);

                // Update Book Rating
                updateBookRating($pdo, $book_id);

                $_SESSION['flash_message'] = "Ulasan berhasil ditambahkan.";
                $_SESSION['flash_type'] = "success";
            }
        } else if ($action === 'edit') {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, comment = ? WHERE id = ?");
            $stmt->execute([$rating, $comment, $id]);

            // Get book_id to update average rating
            $get_book = $pdo->prepare("SELECT book_id FROM reviews WHERE id = ?");
            $get_book->execute([$id]);
            $b_id = $get_book->fetchColumn();
            if ($b_id)
                updateBookRating($pdo, $b_id);

            $_SESSION['flash_message'] = "Ulasan berhasil diperbarui.";
            $_SESSION['flash_type'] = "success";
        }
        redirect('index.php?page=admin_reviews');
    }
}

// Function to Recalculate Book Rating
function updateBookRating($pdo, $book_id)
{
    $stmt = $pdo->prepare("SELECT AVG(rating) FROM reviews WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $avg = $stmt->fetchColumn();

    $update = $pdo->prepare("UPDATE books SET rating = ? WHERE id = ?");
    $update->execute([$avg ?: 0, $book_id]);
}

// Fetch Reviews with related data
$query = "
    SELECT r.*, u.name as user_name, u.email as user_email, b.title as book_title, b.image as book_image, b.author as book_author
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN books b ON r.book_id = b.id
    ORDER BY r.created_at DESC
";
$reviews = $pdo->query($query)->fetchAll();

// Fetch Books for Dropdown (for adding reviews)
$books = $pdo->query("SELECT id, title FROM books ORDER BY title ASC")->fetchAll();
?>

<div class="max-w-7xl mx-auto mb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-4xl font-serif font-bold text-slate-900 mb-2">Manajemen Ulasan</h2>
            <p class="text-slate-500 font-sans">Moderasi ulasan dan rating pengguna.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openModal('add')"
                class="px-5 py-2.5 bg-slate-900 text-white rounded-xl shadow-lg hover:shadow-xl hover:bg-primary transition-all transform hover:-translate-y-0.5 flex items-center gap-2 font-bold text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Buat Ulasan
            </button>
            <a href="index.php?page=admin"
                class="px-5 py-2.5 bg-white text-slate-600 rounded-xl border border-slate-200 hover:bg-slate-50 hover:text-primary font-medium transition-all shadow-sm flex items-center gap-2">
                Kembali
            </a>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="grid grid-cols-1 gap-6">
        <?php foreach ($reviews as $r): ?>
            <div
                class="bg-white p-6 rounded-[2rem] shadow-xl border border-slate-100 flex flex-col md:flex-row gap-6 hover:shadow-2xl transition-shadow group">
                <!-- Book Cover (Mini) -->
                <div
                    class="w-full md:w-32 h-48 md:h-40 flex-shrink-0 rounded-2xl overflow-hidden shadow-md bg-slate-200 relative">
                    <img src="assets/images/<?= htmlspecialchars($r['book_image']) ?>" alt="Book Cover"
                        class="w-full h-full object-cover">
                    <div
                        class="absolute top-2 right-2 bg-white/90 backdrop-blur px-2 py-1 rounded-lg text-xs font-bold shadow-sm flex items-center gap-1">
                        ⭐
                        <?= number_format($r['rating'], 1) ?>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-lg text-slate-900 mb-1">
                                    <?= htmlspecialchars($r['book_title']) ?>
                                </h3>
                                <p class="text-sm text-slate-500 mb-3">oleh <span class="italic">
                                        <?= htmlspecialchars($r['book_author']) ?>
                                    </span></p>
                            </div>
                            <div class="text-right hidden md:block">
                                <span
                                    class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold border border-slate-200">
                                    <?= date('d M Y, H:i', strtotime($r['created_at'])) ?>
                                </span>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-xs shadow-md">
                                <?= strtoupper(substr($r['user_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">
                                    <?= htmlspecialchars($r['user_name']) ?>
                                </div>
                                <div class="text-xs text-slate-400">
                                    <?= htmlspecialchars($r['user_email']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Comment Bubble -->
                        <div
                            class="relative bg-slate-50 p-4 rounded-2xl rounded-tl-none border border-slate-100 text-slate-700 text-sm leading-relaxed italic">
                            "
                            <?= nl2br(htmlspecialchars($r['comment'])) ?>"
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-slate-50">
                        <button onclick='openModal("edit", <?= json_encode($r) ?>)'
                            class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 font-bold text-sm transition-colors flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            Edit
                        </button>
                        <form method="POST" onsubmit="return confirm('Hapus ulasan ini?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <button type="submit"
                                class="px-4 py-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 font-bold text-sm transition-colors flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($reviews)): ?>
            <div class="p-12 text-center text-slate-400 bg-white rounded-[2rem] shadow-sm border border-slate-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-50" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <p class="text-lg font-medium">Belum ada ulasan yang masuk.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div id="reviewModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">

                <div class="bg-slate-50 px-8 py-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-slate-900" id="modalTitle">Edit Ulasan</h3>
                    <button type="button" onclick="closeModal()"
                        class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 hover:bg-rose-100 hover:text-rose-500 flex items-center justify-center transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" id="reviewForm">
                    <input type="hidden" name="action" id="formAction" value="edit">
                    <input type="hidden" name="id" id="reviewId" value="">

                    <div class="p-8 space-y-6">
                        <!-- Book Selection (Only for Add) -->
                        <div id="bookSelectContainer" class="hidden">
                            <label class="block text-sm font-bold text-slate-700 mb-1">Pilih Buku</label>
                            <div class="relative">
                                <select name="book_id" id="book_id"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all appearance-none cursor-pointer">
                                    <?php foreach ($books as $book): ?>
                                        <option value="<?= $book['id'] ?>">
                                            <?= htmlspecialchars($book['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Rating</label>
                            <div class="flex gap-4 items-center">
                                <div class="rating flex flex-row-reverse justify-end gap-1">
                                    <input type="radio" name="rating" id="star5" value="5" class="peer hidden"
                                        required />
                                    <label for="star5"
                                        class="cursor-pointer text-slate-300 peer-checked:text-amber-400 hover:text-amber-400 peer-hover:text-amber-400 transition-colors">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </label>
                                    <input type="radio" name="rating" id="star4" value="4" class="peer hidden" />
                                    <label for="star4"
                                        class="cursor-pointer text-slate-300 peer-checked:text-amber-400 hover:text-amber-400 peer-hover:text-amber-400 transition-colors">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </label>
                                    <input type="radio" name="rating" id="star3" value="3" class="peer hidden" />
                                    <label for="star3"
                                        class="cursor-pointer text-slate-300 peer-checked:text-amber-400 hover:text-amber-400 peer-hover:text-amber-400 transition-colors">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </label>
                                    <input type="radio" name="rating" id="star2" value="2" class="peer hidden" />
                                    <label for="star2"
                                        class="cursor-pointer text-slate-300 peer-checked:text-amber-400 hover:text-amber-400 peer-hover:text-amber-400 transition-colors">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </label>
                                    <input type="radio" name="rating" id="star1" value="1" class="peer hidden" />
                                    <label for="star1"
                                        class="cursor-pointer text-slate-300 peer-checked:text-amber-400 hover:text-amber-400 peer-hover:text-amber-400 transition-colors">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Comment -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Komentar</label>
                            <textarea name="comment" id="comment" rows="4"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-slate-400 resize-none"
                                placeholder="Tulis ulasan..."></textarea>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-slate-100">
                        <button type="submit"
                            class="px-6 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-primary transition-colors shadow-lg shadow-slate-200">
                            Simpan
                        </button>
                        <button type="button" onclick="closeModal()"
                            class="px-6 py-3 bg-white text-slate-600 font-bold rounded-xl border border-slate-300 hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('reviewModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('reviewForm');
    const formAction = document.getElementById('formAction');
    const reviewIdInput = document.getElementById('reviewId');
    const bookSelectContainer = document.getElementById('bookSelectContainer');

    function openModal(mode, data = null) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        if (mode === 'edit' && data) {
            modalTitle.textContent = 'Edit Ulasan';
            formAction.value = 'edit';
            reviewIdInput.value = data.id;
            bookSelectContainer.classList.add('hidden');

            // Set Rating
            const ratingInput = document.querySelector(`input[name="rating"][value="${data.rating}"]`);
            if (ratingInput) ratingInput.checked = true;

            document.getElementById('comment').value = data.comment;
        } else {
            modalTitle.textContent = 'Buat Ulasan Baru';
            formAction.value = 'add';
            reviewIdInput.value = '';
            bookSelectContainer.classList.remove('hidden');
            form.reset();
        }
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close on Escape key
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Rating star interaction
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    ratingInputs.forEach(input => {
        input.addEventListener('change', function () {
            // Optional: Add visual feedback logic if needed beyond CSS peer-checked
        });
    });
</script>
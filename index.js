// Logique de la maquette Cybersens Academy
// - ouverture/fermeture du popup "bonnes pratiques" avec persistance
// - génération des contenus de cours
// - quiz: génération, validation, score

const dialog = document.getElementById('goodPractices');
const btnOpenTips = document.getElementById('openTips');
const btnRemember = document.getElementById('rememberTips');
const btnCloseTips = document.getElementById('closeTips');

// Affichage initial du popup sauf si l'utilisateur a choisi de ne plus l'afficher
const GP_KEY = 'cybersens_hide_tips';
if (!localStorage.getItem(GP_KEY)) {
	try { dialog.showModal(); } catch {}
}

btnOpenTips?.addEventListener('click', ()=>{ try{ dialog.showModal(); }catch{} });
btnRemember?.addEventListener('click', ()=>{ localStorage.setItem(GP_KEY, '1'); });
btnCloseTips?.addEventListener('click', ()=>{});

// Cours: contenu minimal mock
const COURSES = {
	phishing: {
		title: 'Reconnaître le phishing',
		parts: [
			'Vérifiez l\'adresse de l\'expéditeur et les fautes d\'orthographe.',
			'Survolez les liens pour inspecter l\'URL réelle.',
			'Ne fournissez jamais de données sensibles par email.'
		]
	},
	mdp: {
		title: 'Gestion des mots de passe',
		parts: [
			'Utilisez un gestionnaire de mots de passe.',
			'Créez des mots de passe longs (12+ caractères) et uniques.',
			'Activez la MFA pour les comptes critiques.'
		]
	},
	reseau: {
		title: 'Réseaux publics',
		parts: [
			'Évitez les connexions aux Wi‑Fi ouverts sans VPN.',
			'Désactivez le partage de fichiers sur les réseaux publics.',
			'Mettez à jour votre OS et vos antivirus.'
		]
	},
	rgpd: {
		title: 'Protection des données',
		parts: [
			'Collectez le minimum de données nécessaires.',
			'Chiffrez les données au repos et en transit.',
			'Respectez le droit d\'accès et d\'effacement.'
		]
	}
};

const courseDetail = document.getElementById('courseDetail');
const courseContent = document.getElementById('courseContent');
const closeDetail = document.getElementById('closeDetail');
const startQuizFromCourse = document.getElementById('startQuizFromCourse');

document.querySelectorAll('[data-start-course]').forEach(btn => {
	btn.addEventListener('click', () => {
		const card = btn.closest('.cours-card');
		const id = card?.dataset.courseId;
		const course = id ? COURSES[id] : null;
		if (!course) return;
		renderCourse(course);
	});
});

function renderCourse(course){
	courseContent.innerHTML = '';
	const h3 = document.createElement('h3');
	h3.textContent = course.title;
	courseContent.appendChild(h3);
	const ul = document.createElement('ul');
	course.parts.forEach(p => { const li = document.createElement('li'); li.textContent = p; ul.appendChild(li); });
	courseContent.appendChild(ul);
	courseDetail.hidden = false;
	courseDetail.scrollIntoView({behavior:'smooth'});
}

closeDetail?.addEventListener('click', ()=>{ courseDetail.hidden = true; });
startQuizFromCourse?.addEventListener('click', ()=>{ document.getElementById('quiz')?.scrollIntoView({behavior:'smooth'}); });

// Quiz
const QUIZ = [
	{
		q: 'Quel est le signe fréquent d\'un email de phishing ?',
		type: 'single',
		options: [
			'Adresse d\'expéditeur suspecte ou mal orthographiée',
			'Signature complète et polie',
			'Sujet clair et concis'
		],
		answer: 0
	},
	{
		q: 'Quelles bonnes pratiques améliorent la sécurité ? (choix multiples)',
		type: 'multi',
		options: [
			'Réutiliser le même mot de passe partout',
			'Activer l\'authentification multi-facteur',
			'Mettre à jour régulièrement ses logiciels'
		],
		answer: [1,2]
	},
	{
		q: 'Sur un Wi‑Fi public, que faut-il éviter ?',
		type: 'single',
		options: [
			'Utiliser un VPN',
			'Accéder à son compte bancaire',
			'Désactiver le partage de fichiers'
		],
		answer: 1
	}
];

const quizItems = document.getElementById('quizItems');
const quizForm = document.getElementById('quizForm');
const quizResult = document.getElementById('quizResult');
const resetQuiz = document.getElementById('resetQuiz');

function renderQuiz(){
	quizItems.innerHTML = '';
	QUIZ.forEach((item, idx) => {
		const wrap = document.createElement('div');
		wrap.className = 'quiz-item';
		const title = document.createElement('h4');
		title.textContent = `Q${idx+1}. ${item.q}`;
		wrap.appendChild(title);
		const name = `q_${idx}`;
		if(item.type === 'single'){
			item.options.forEach((opt, i)=>{
				const id = `${name}_${i}`;
				const label = document.createElement('label');
				label.style.display = 'block';
				label.innerHTML = `<input type="radio" name="${name}" value="${i}" id="${id}"> ${opt}`;
				wrap.appendChild(label);
			});
		} else {
			item.options.forEach((opt, i)=>{
				const id = `${name}_${i}`;
				const label = document.createElement('label');
				label.style.display = 'block';
				label.innerHTML = `<input type="checkbox" name="${name}" value="${i}" id="${id}"> ${opt}`;
				wrap.appendChild(label);
			});
		}
		quizItems.appendChild(wrap);
	});
}

renderQuiz();

quizForm?.addEventListener('submit', (e)=>{
	e.preventDefault();
	let score = 0;
	QUIZ.forEach((item, idx)=>{
		const name = `q_${idx}`;
		if(item.type === 'single'){
			const sel = quizForm.querySelector(`input[name="${name}"]:checked`);
			if(sel && Number(sel.value) === item.answer) score++;
		} else {
			const boxes = quizForm.querySelectorAll(`input[name="${name}"]:checked`);
			const picks = Array.from(boxes).map(b=>Number(b.value)).sort();
			const ans = [...item.answer].sort();
			if(JSON.stringify(picks) === JSON.stringify(ans)) score++;
		}
	});
	quizResult.textContent = `Score: ${score}/${QUIZ.length}`;
});

resetQuiz?.addEventListener('click', ()=>{
	quizForm.reset();
	quizResult.textContent = '';
});


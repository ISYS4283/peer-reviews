# Peer Reviews

Read the assigned blog posts and score them 0-100 each.

Create a table in your database called `peer_reviews` using [this data definition language][1].
If you need a refresher on how to connect to the database server and execute queries, then
refer to [the questions-answers video][2].

Insert your score for each review. For example:

```sql
INSERT INTO peer_reviews (post_url, score) VALUES ('https://blog.isys4283.walton.uark.edu/jpucket/?p=7', 97);
```
[1]:./ddl.sql
[2]:https://github.com/ISYS4283/questions-answers
